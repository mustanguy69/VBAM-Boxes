<?php
/**
 * @author Kurnia Wijanto
 * @copyright Copyright (c) 2020 Visy IDT
 * @package Visy_Shell
 *
 * path: app/code/Visy/Shell/Command/IntegrationSalesOrderCommand.php
 */

namespace Visy\Shell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Scommerce\Custom\Cron\CustomProcess;

/**
 * Class IntegrationSalesOrderCommand
 */
class IntegrationSalesOrderCommand extends Command {

    const CONFIG_INTEGRATION_SALES_ORDER_ACTIVATE = 'visy_integration/salesorder/active';
    const CONFIG_WEB_SECURE_BASE_URL = 'web/secure/base_url';
    const CONFIG_INTEGRATION_SALES_ORDER_WEBSERVICE_URL = 'visy_integration/salesorder/webservice_url';
    const CONFIG_INTEGRATION_SALES_ORDER_CONSUMER_KEY = 'visy_integration/salesorder/consumer_key';
    const CONFIG_INTEGRATION_SALES_ORDER_CONSUMER_SECRET = 'visy_integration/salesorder/consumer_secret';
    const CONFIG_INTEGRATION_SALES_ORDER_ACCESS_TOKEN = 'visy_integration/salesorder/access_token';
    const CONFIG_INTEGRATION_SALES_ORDER_ACCESS_TOKEN_SECRET = 'visy_integration/salesorder/access_token_secret';

    const STATUS_SKIP = 'SKIP';
    const STATUS_IN_PROCESS = 'IN_PROCESS';
    const STATUS_FAILED = 'FAILED';
    const STATUS_SENT = 'SENT';

    private $_state;
    private $_scopeConfig;
    private $_orderCollectionFactory;
    private $_directoryList;
    private $_output;
    private $_customProcess;


    public function __construct(
        State $state,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $orderCollectionFactory,
        DirectoryList $directoryList
    ) {
        $this->_state = $state;
        $this->_scopeConfig = $scopeConfig;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_directoryList = $directoryList;

        parent::__construct();
    }

    protected function configure() {
        // set command name & description.
        $this->setName('visy:integration:sales-order')->setDescription('Visy sales order integration');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->_output = $output;

        try {
            $this->_state->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_CRONTAB,
                [$this, "executeCallBack"],
                [$input, $output]
            );
        } catch(\Exception $e) {
        }

        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $restApiUrl = $this->_scopeConfig->getValue(self::CONFIG_WEB_SECURE_BASE_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);

        // STEP 1: loop new orders
        $orderCollection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('sap_sent_status', array('null' => true))
            ->addFieldToFilter('sap_sent_date', array('null' => true))
            ->setOrder('created_at', 'ASC');

        // STEP 2: update all orders "sap_sent_status" to "IN_PROCESS"
        foreach($orderCollection as $order) {
            $order->setData('sap_sent_status', self::STATUS_IN_PROCESS);
            $order->save();
        }
        $this->logMessage("info", "Start processing {$orderCollection->count()} sales orders.");

        $count = 0;
        foreach($orderCollection as $order) {
            // STEP 3: Load "integration" configuration
            $configIsActive = $this->_scopeConfig->getValue(self::CONFIG_INTEGRATION_SALES_ORDER_ACTIVATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $order->getStoreId());

            $webServiceUrl = $this->_scopeConfig->getValue(self::CONFIG_INTEGRATION_SALES_ORDER_WEBSERVICE_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $order->getStoreId());
            $consumerKey = $this->_scopeConfig->getValue(self::CONFIG_INTEGRATION_SALES_ORDER_CONSUMER_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $order->getStoreId());
            $consumerSecret = $this->_scopeConfig->getValue(self::CONFIG_INTEGRATION_SALES_ORDER_CONSUMER_SECRET, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $order->getStoreId());
            $accessToken = $this->_scopeConfig->getValue(self::CONFIG_INTEGRATION_SALES_ORDER_ACCESS_TOKEN, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $order->getStoreId());
            $accessTokenSecret = $this->_scopeConfig->getValue(self::CONFIG_INTEGRATION_SALES_ORDER_ACCESS_TOKEN_SECRET, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $order->getStoreId());

            // STEP 4: validate if "sales order integration" enabled on "stores" scope level.
            if(!$configIsActive || empty($configIsActive)) {
                $order->setData('sap_sent_status', self::STATUS_SKIP);
                $order->save();

                $this->logMessage("comment", "[SKIP] order #{$order->getIncrementId()} sales order integration disabled (store ID: {$order->getStoreId()}).");
                continue;
            }
            if(empty($webServiceUrl) || empty($consumerKey) || empty($consumerSecret) || empty($accessToken) || empty($accessTokenSecret)) {

                $order->setData('sap_sent_status', self::STATUS_SKIP);
                $order->save();

                $this->logMessage("comment", "[SKIP] order #{$order->getIncrementId()} invalid integration configuration (store ID: {$order->getStoreId()}).");
                continue;
            }

            // STEP 5: Skip invalid "payment status" SecurePay payment method.
            $status = $order->getStatus();
            $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
            if($paymentMethod == 'sfdirectpost' && $status != 'processing') {
                $order->setData('sap_sent_status', self::STATUS_SKIP);
                $order->save();

                $this->logMessage("comment", "[SKIP] order: #{$order->getIncrementId()} invalid SecurePay payment status (store ID: {$order->getStoreId()}, payment status: {$status}).");
                continue;
            }

            // STEP 6: consume Magento "/rest/V1/orders/:orderId" REST API.
            $orderData = $this->getApiSalesOrderByOrderId(
                $restApiUrl,
                $order->getEntityId(),
                $consumerKey,
                $consumerSecret,
                $accessToken,
                $accessTokenSecret
            );

            // STEP 7: send payload data to Biztalk.
            if(!empty($orderData)) {
                if($this->sendOrderToBiztalk($webServiceUrl, $orderData)) {
                    // STEP 8: update "sap_sent_status" to "SENT".
                    $order->setData('sap_sent_status', self::STATUS_SENT);
                    $order->setData('sap_sent_date', date("Y-m-d H:i:s"));
                    $order->save();

                    $count++;
                    $this->logMessage("info", "[SENT] order #{$order->getIncrementId()} successfully sent (store ID: {$order->getStoreId()}).");
                }
            } else {
                $order->setData('sap_sent_status', self::STATUS_FAILED);
                $order->save();

                $this->logMessage("comment", "[FAILED] order #{$order->getIncrementId()} invalid sales order payload (store ID: {$order->getStoreId()}).");
            }
        }
        $this->logMessage("info","Total {$count} of {$orderCollection->count()} successfully sent.");
    }

    protected function executeCallBack(InputInterface $input, OutputInterface $output) {
        $this->_customProcess = $this->objectManager->create(CustomProcess::class);
        $this->_customProcess->execute();
        return Cli::RETURN_SUCCESS;
    }

    protected function logMessage($status, $message) {
        $datetime = date("Y-m-d H:i:sA T");
        $this->_output->writeln("[{$datetime}] <{$status}>{$message}</{$status}>");
        file_put_contents($this->_directoryList->getPath('log') . '/visy_integration_sales_order.log', "[$datetime] $message".PHP_EOL, FILE_APPEND);
    }

    protected function generateSignature($method, $url, $data, $consumerSecret, $tokenSecret) {
	      $url = rawurlencode($url);
          $data = rawurlencode(http_build_query($data, '', '&'));
          $data = implode('&', [$method, $url, $data]);
          $secret = implode('&', [$consumerSecret, $tokenSecret]);

          return base64_encode(hash_hmac('sha1', $data, $secret, true));
    }

    protected function getApiSalesOrderByOrderId($restApiUrl, $orderId, $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret) {
        try {
            $method = 'GET';
            $timestamp = time();
            $nonce = md5(uniqid(rand(), true));
            $signatureMethod = 'HMAC-SHA1';
            $version = '1.0';
            $url = "{$restApiUrl}rest/V1/orders/{$orderId}";

            $data = [
                'oauth_consumer_key' => $consumerKey,
                'oauth_nonce' => md5($nonce),
                'oauth_signature_method' => $signatureMethod,
                'oauth_timestamp' => $timestamp,
                'oauth_token' => $accessToken,
                'oauth_version' => $version,
            ];
            $data['oauth_signature'] = $this->generateSignature($method, $url, $data, $consumerSecret, $accessTokenSecret);


            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => [
                    'Authorization: OAuth ' . http_build_query($data, '', ',')
                ]
            ]);

            $result = curl_exec($curl);
            curl_close($curl);
            return $result;

        } catch(Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    protected static function sendOrderToBiztalk($webServiceUrl, $data) {
        try {
            // Submit the output JSON sales order to BizTalk.
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $webServiceUrl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if(in_array($status, array(200, 202))) {
                return true;
            } else {
                return false;
            }
        } catch(Exception $e) {
            return false;
        }
    }
}
