<?php
namespace Appscore\SecurePay\Model;
 

class SecurePay extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'appscore_securepay';
 
    protected $_code = self::CODE;
 
    protected $_canAuthorize = true;
    protected $_canCapture = true;

    protected $_curl;
    protected $_scopeConfig;
    protected $_request;
    protected $_messageManager;
    protected $_resultRedirect;

    public function __construct( 
        \Magento\Framework\Model\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, 
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, 
        \Magento\Payment\Helper\Data $paymentData, 
        \Magento\Payment\Model\Method\Logger $logger, 
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\ResultFactory $resultRedirect,
        array $data = array() 
        ) { 
        parent::__construct( $context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $moduleList, $localeDate, null, null, $data ); 
        $this->_countryFactory = $countryFactory;
        $this->_curl = $curl;
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_resultRedirect = $resultRedirect;
    } 

    public function validate()
    {
        /*
         * calling parent validate function
         */

        
        return $this;
    }

    /**
     * Set value after save payment from post data to use in case capture or authorize
     * @param \Magento\Framework\DataObject $data
     * @return $this
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $this->getInfoInstance()->setAdditionalInformation('post_data_value', $data->getData());

        return $this;
    }

    /**
     * Capture Payment.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

        try{
            $order = $payment->getOrder();
            $info = $this->getInfoInstance();
            $token = $info->getAdditionalInformation()['post_data_value']['additional_data']['token'];

            $merchantCode = $this->_scopeConfig->getValue(
                'payment/appscore_securepay/merchant_code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    
            $clientId = $this->_scopeConfig->getValue(
                'payment/appscore_securepay/client_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    
            $clientSecret = $this->_scopeConfig->getValue(
                'payment/appscore_securepay/client_secret',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $sandbox = $this->_scopeConfig->getValue(
                'payment/appscore_securepay/sandbox',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $fraudDetection = $this->_scopeConfig->getValue(
                'payment/appscore_securepay/fraud_detection',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            // auth to securepay api -> return access_token
            $auth = $this->authentificationSecurePayApi($merchantCode, $clientId, $clientSecret, $sandbox);
            

            if($auth['access_token']) {
                $request = [
                    "amount" => bcmul($amount, 100),
                    "merchant_code" => $merchantCode,
                    "token" => $token,
                    "auth_token" => $auth['access_token'],
                    "order_id" => $this->genUuid(),
                    "user_ip" => $order->getRemoteIp(),
                    "sandbox" => $sandbox,
                    "shipping_country_code" => $this->getIso3CountryCode($order->getShippingAddress()->getCountryId()),
                    "shipping_city" => $order->getShippingAddress()->getCity(),
                    "shipping_postcode" => $order->getShippingAddress()->getPostcode(),
                    "billing_country_code" => $this->getIso3CountryCode($order->getBillingAddress()->getCountryId())
                ];
                
                if($fraudDetection == 1) {
                    //make fraud detection
                    $fraud = $this->makeFraudDetectionSystem($request);

                    //todo test with client account
                    if($fraud['fraudCheckResult']['score'] < 30) {
                        // make preauth of payment
                        $preauth = $this->makeAuthRequest($request);
                    } else {
                        $this->_messageManager->addErrorMessage('A fraud have been detected on this credit card, payment aborted');
                        throw new \Magento\Framework\Validator\Exception(__('A fraud have been detected on this credit card, payment aborted'));
                        
                        return $this;
                    }
                } else {
                    $preauth = $this->makeAuthRequest($request);
                }
            }
            
            if($preauth['status'] == "paid") {
                $payment->setTransactionId($preauth['bankTransactionId']);
                $payment->setParentTransactionId($preauth['bankTransactionId']);
                $payment->setIsTransactionClosed(0);

                $capture = $this->makeCaptureRequest($request);
                
            } else {
                $this->cancelAuthRequest($request);
                $payment->setTransactionId($preauth['bankTransactionId'] . '-' . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
                    ->setParentTransactionId($preauth['bankTransactionId'])
                    ->setIsTransactionClosed(1)
                    ->setShouldCloseParentTransaction(1);

                $this->_messageManager->addErrorMessage('The payment failed after authorize.');
                throw new \Magento\Framework\Validator\Exception(__('The payment failed after authorize.'));
                
                return $this;
            }
            
           if(!$capture['status'] == "paid") {
                $this->cancelCaptureRequest($request);
                $payment->setTransactionId($capture['bankTransactionId'] . '-' . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
                    ->setParentTransactionId($capture['bankTransactionId'])
                    ->setIsTransactionClosed(1)
                    ->setShouldCloseParentTransaction(1);

                $this->_messageManager->addErrorMessage('The payment capture failed.');
                throw new \Magento\Framework\Validator\Exception(__('The payment capture failed.'));

                return $this;
           } else {
                $payment->setTransactionId($capture['bankTransactionId']) ->setIsTransactionClosed(1);

                return $this;
           }
            
        }
        catch (\Exception $e) {
            $this->_messageManager->addErrorMessage('The payment failed.');
            $this->_logger->critical('Error Curl', ['exception' => $e]);
            throw new \Magento\Framework\Validator\Exception(__('The payment failed.'));
        }
    }

    public function getIso3CountryCode($countryId) {
        $country = $this->_countryFactory->create()->loadByCode($countryId);
        
        $iso3Code = $country->getData('iso3_code');
        
        return $iso3Code;
    }

    public function authentificationSecurePayApi($merchantCode, $clientId, $clientSecret, $sandbox) 
    {
        try {
            if($sandbox == 1) {
                $url = "https://hello.sandbox.auspost.com.au/oauth2/ausujjr7T0v0TTilk3l5/v1/token";
            } else {
                $url = "https://hello.auspost.com.au/oauth2/ausrkwxtmx9Jtwp4s356/v1/token";
            }
            
            $this->_curl->addHeader("Content-Type", "application/x-www-form-urlencoded");
            $this->_curl->addHeader("Authorization", "Basic " . base64_encode($clientId . ":" . $clientSecret));
            $params = "grant_type=client_credentials&scope=https%3A%2F%2Fapi.payments.auspost.com.au%2Fpayhive%2Fpayments%2Fread%20https%3A%2F%2Fapi.payments.auspost.com.au%2Fpayhive%2Fpayments%2Fwrite%20https%3A%2F%2Fapi.payments.auspost.com.au%2Fpayhive%2Fpayment-instruments%2Fread%20https%3A%2F%2Fapi.payments.auspost.com.au%2Fpayhive%2Fpayment-instruments%2Fwrite";
    
            $this->_curl->post($url, $params);
            $response = $this->_curl->getBody();
            $response = json_decode($response, true);

            return $response;

        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(__('Failed authentification SecurePay.'));
            $this->_logger->critical('Error Curl', ['exception' => $e]);
        }
        
    }
 
    /**
     * Set the payment action to authorize_and_capture
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return self::ACTION_AUTHORIZE_CAPTURE;
    }
 
    public function makeFraudDetectionSystem($request)
    {
        try {

            if($request['sandbox'] == 1) {
                $url = "https://payments-stest.npe.auspost.zone/v2/antifraud/check";
            } else {
                $url = "https://payments.auspost.net.au/v2/antifraud/check";
            }
    
            $this->_curl->addHeader("Content-Type", "application/json");
            $this->_curl->addHeader("Authorization", "Bearer ".  $request['auth_token']);
            $params = '{
                "fraudCheckType": "FRAUD_GUARD",
                "ip": "'.$request['user_ip'].'",
                "merchantCode": "'. $request['merchant_code'] .'",
                "orderId": "'. $request['order_id'] .'",
                "paymentDetails": {
                    "amount": "'.$request['amount'].'",
                    "token": "'. $request['token'] .'",
                    "paymentMethod": "PAYMENT_CARD"
                },
                "shippingAddress": {
                    "city": "'.$request['shipping_city'].'",
                    "postcode": "'.$request['shipping_postcode'].'",
                    "countryCode": "'.$request['shipping_country_code'].'"
                },
                "billingAddress": {
                    "countryCode": "'.$request['billing_country_code'].'"
                }
            }';
            
            $this->_curl->post($url, $params);
            $response = $this->_curl->getBody();
            $response = json_decode($response, true);

            return $response;

        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(__('Failed fraud detect request.'));
            $this->_logger->critical('Error Curl', ['exception' => $e]);
        }
    }

    public function makeAuthRequest($request)
    {
        try {

            if($request['sandbox'] == 1) {
                $url = "https://payments-stest.npe.auspost.zone/v2/payments/preauths";
            } else {
                $url = "https://payments.auspost.net.au/v2/payments/preauths";
            }
    
            $this->_curl->addHeader("Content-Type", "application/json");
            $this->_curl->addHeader("Authorization", "Bearer ".  $request['auth_token']);
            $params = '{"amount": "'.$request['amount'].'", "preAuthType": "INITIAL_AUTH", "merchantCode": "'. $request['merchant_code'] .'", "token": "'. $request['token'] .'", "ip": "'. $request['user_ip'] .'", "orderId": "'. $request['order_id'] .'"}';
            
            $this->_curl->post($url, $params);
            $response = $this->_curl->getBody();
            $response = json_decode($response, true);

            return $response;

        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(__('Failed authorize request.'));
            $this->_logger->critical('Error Curl', ['exception' => $e]);
        }
    }

    public function cancelAuthRequest($request)
    {
        try {

            if($request['sandbox'] == 1) {
                $url = "https://payments-stest.npe.auspost.zone/v2/payments/preauths/".$request['order_id']."/cancel";
            } else {
                $url = "https://payments.auspost.net.au/v2/payments/preauths/".$request['order_id']."/cancel";
            }

            $this->_curl->addHeader("Content-Type", "application/json");
            $this->_curl->addHeader("Authorization", "Bearer ".  $request['auth_token']);
            $params = '{"merchantCode": "'. $request['merchant_code'] .'", "ip": "'. $request['user_ip'] .'"}';
            
            $this->_curl->post($url, $params);
            $response = $this->_curl->getBody();
            $response = json_decode($response, true);

            return $response;

        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(__('Failed canceling authorize request.'));
            $this->_logger->critical('Error Curl', ['exception' => $e]);
        }
    }
 
    public function makeCaptureRequest($request)
    {
        try {

            if($request['sandbox'] == 1) {
                $url = "https://payments-stest.npe.auspost.zone/v2/payments/preauths/".$request['order_id']."/capture";
            } else {
                $url = "https://payments.auspost.net.au/v2/payments/preauths/".$request['order_id']."/capture";
            }

            $this->_curl->addHeader("Content-Type", "application/json");
            $this->_curl->addHeader("Authorization", "Bearer ".  $request['auth_token']);
            $params = '{"amount": "'.$request['amount'].'", "merchantCode": "'. $request['merchant_code'] .'", "token": "'. $request['token'] .'", "ip": "'.$request['user_ip'].'", "orderId": "'. $request['order_id'] .'"}';

            $this->_curl->post($url, $params);
            $response = $this->_curl->getBody();
            $response = json_decode($response, true);

            return $response;

        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(__('Failed capture payment.'));
            $this->_logger->critical('Error Curl', ['exception' => $e]);
        }
    }

    public function cancelCaptureRequest($request)
    {
        try {
    
            if($request['sandbox'] == 1) {
                $url = "https://payments-stest.npe.auspost.zone/v2/orders/".$request['order_id']."/refunds";
            } else {
                $url = "https://payments.auspost.net.au/v2/orders/".$request['order_id']."/refunds";
            }

            $url = "https://payments.auspost.net.au/v2/orders/".$request['order_id']."/refunds";
            $this->_curl->addHeader("Content-Type", "application/json");
            $this->_curl->addHeader("Authorization", "Bearer ".  $request['auth_token']);
            $params = '{"merchantCode": "'. $request['merchant_code'] .'", "ip": "'. $request['user_ip'] .'"}';

            $this->_curl->post($url, $params);
            $response = $this->_curl->getBody();
            $response = json_decode($response, true);

            return $response;

        } catch (\Exception $e) {
            throw new \Magento\Framework\Validator\Exception(__('Failed cancel capture payment.'));
            $this->_logger->critical('Error Curl', ['exception' => $e]);
        }
    }

    function genUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}