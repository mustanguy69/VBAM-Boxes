<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     BNCNLookup
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\BNCNLookup\Model\Api;

use Psr\Log\LoggerInterface;

class Lookup extends AbstractApi
{
    const HTTP_VERSION = '1.1';
    const BUSINESS_NUMBER = 'BN';
    const COMPANY_NUMBER = 'CN';

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    private $curl;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Edm constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writer
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->curl = $curlFactory;
        $this->logger = $logger;

        parent::__construct($scopeConfig, $storeManager, $data);
    }

    /**
     * Validate ABN/ACN
     * @param null $lookupString
     * @return string
     */
    public function checkValidity($lookupString = null)
    {
        try {
            //Business Number Request
            $xml = $this->requestBusinessNumberLookup($lookupString);
            $msg = $this->validateBusinessNumberRequest($xml);

            // Company Number Request
            if (!$msg) {
                $xml = $this->requestCompanyNumberLookup($lookupString);
                $msg = $this->validateCompanyNumberRequest($xml);
            }

            return $msg;
        } catch (\Exception $e) {
            $this->logger->error("Error in checkValidity() function in BNCNLookup :" . $e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Validate Response XML
     * @param $xml
     * @param $type
     * @return bool
     */
    private function validate($xml, $type)
    {
        $msg = false;
        $version = ($type == self::BUSINESS_NUMBER) ? $this->getBNServiceVersion() : $this->getCNServiceVersion();
        if ($xml) {
            if ($res = $xml['response']) {
                if ($res->exception) {
                    $msg = false;
                }
                $field = 'businessEntity' . $version;
                if ($res->$field) {
                    $msg = true;
                }
            } else {
                $this->logger->info("Error: No response returned from BNCN Lookup");
                $msg = false;
            }
        }
        return $msg;
    }

    /**
     * Send Request
     * @param $type
     * @param null $lookupString
     * @return array
     * @throws \Exception
     */
    private function request($type, $lookupString = null)
    {
        $searchString = $lookupString ? $lookupString : $this->getTestString();
        $requestUrl = ($type == self::BUSINESS_NUMBER) ? $this->getBNURL() : $this->getCNURL();
        $fields = $this->buildQuery([
            'searchString' => $searchString,
            'includeHistoricalDetails' => $this->getHistoricalData(),
            'authenticationGuid' => $this->getAuthGuid()
        ]);

        $headers    = [ 'Content-Type' => "text/xml" ];
        $response = $this->call(
            \Zend_Http_Client::POST,
            $requestUrl,
            self::HTTP_VERSION,
            $headers,
            $fields
        );

        return (array)simplexml_load_string($response);
    }

    /**
     * Send request for Business Number Lookup
     * @param null $lookupString
     * @return array|null
     */
    public function requestBusinessNumberLookup($lookupString = null)
    {
        try {
            return $this->request(self::BUSINESS_NUMBER, $lookupString);
        } catch (\Exception $e) {
            $this->logger->error("Error in requestBusinessNumberLookup() of BNCNLookup :" . $e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Send request for Company Number Lookup
     * @param null $lookupString
     * @return array|null
     */
    public function requestCompanyNumberLookup($lookupString = null)
    {
        try {
            return $this->request(self::COMPANY_NUMBER, $lookupString);
        } catch (\Exception $e) {
            $this->logger->error("Error in requestCompanyNumberLookup() of BNCNLookup :" . $e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Validate Business Number Request
     * @param $xml
     * @return bool
     */
    public function validateBusinessNumberRequest($xml)
    {
        return $this->validate($xml, self::BUSINESS_NUMBER);
    }

    /**
     * Validate Company Number Request
     * @param $xml
     * @return bool
     */
    public function validateCompanyNumberRequest($xml)
    {
        return $this->validate($xml, self::COMPANY_NUMBER);
    }

    /**
     * REST Call
     *
     * @param $method
     * @param $requestUrl
     * @param $httpVersion
     * @param $headers
     * @param $body
     *
     * @return array|false|mixed|string|string[]
     * @throws \Exception
     */
    private function call($method, $requestUrl, $httpVersion, $headers, $body)
    {
        try {
            $http = $this->curl->create();
            $http->write(
                $method,
                $requestUrl,
                $httpVersion,
                $headers,
                $body
            );

            $response = $http->read();
            $responseOriginal = preg_split('/^\r?$/m', $response, 2);
            $response         = trim($responseOriginal[1]);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $http->close();
        }

        return $response;
    }

    /**
     * Post Exec
     * @param $url
     * @param $fields
     * @return bool|string
     */
    private function post($url, $fields)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        // Receive server response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
    }
}
