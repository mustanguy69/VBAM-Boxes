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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractApi extends DataObject
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    const CONFIG_PATH_AUTH_GUID = 'visy_bnl/general/authentication_guid';
    const CONFIG_PATH_SERVICE_URL = 'visy_bnl/general/service_url';
    const CONFIG_PATH_SERVICE_DIR = 'visy_bnl/general/service_directory';
    const CONFIG_PATH_BN_SERVICE = 'visy_bnl/bn/service';
    const CONFIG_PATH_BN_SERVICE_VERSION = 'visy_bnl/bn/version';
    const CONFIG_PATH_CN_SERVICE = 'visy_bnl/cn/service';
    const CONFIG_PATH_CN_SERVICE_VERSION = 'visy_bnl/cn/version';
    const CONFIG_PATH_HISTORYCAL_DATA = 'visy_bnl/general/historical_details';
    const CONFIG_PATH_TEST_STRING = 'visy_bnl/test/test_string';

    /**
     * AbstractApi constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($data);
    }

    /**
     * Get Auth Guid
     * @return string
     */
    public function getAuthGuid()
    {
        $value = $this->getScopeConfigValues(self::CONFIG_PATH_AUTH_GUID);

        return $value;
    }

    /**
     * Get Business Number URL
     * @return string
     */
    public function getBNURL()
    {
        $url = $this->getScopeConfigValues(self::CONFIG_PATH_SERVICE_URL);
        $dir = $this->getScopeConfigValues(self::CONFIG_PATH_SERVICE_DIR);
        $service = $this->getScopeConfigValues(self::CONFIG_PATH_BN_SERVICE) .
            $this->getScopeConfigValues(self::CONFIG_PATH_BN_SERVICE_VERSION);

        return $url . $dir . $service;
    }

    /**
     * Get BN Service Version
     * @return false|string
     */
    public function getBNServiceVersion()
    {
        return substr($this->getScopeConfigValues(self::CONFIG_PATH_BN_SERVICE_VERSION), 1);
    }

    /**
     * Get CN Service Version
     * @return false|string
     */
    public function getCNServiceVersion()
    {
        return substr($this->getScopeConfigValues(self::CONFIG_PATH_CN_SERVICE_VERSION), 1);
    }

    /**
     * Get Company Number URL
     * @return string
     */
    public function getCNURL()
    {
        $url = $this->getScopeConfigValues(self::CONFIG_PATH_SERVICE_URL);
        $dir = $this->getScopeConfigValues(self::CONFIG_PATH_SERVICE_DIR);
        $service = $this->getScopeConfigValues(self::CONFIG_PATH_CN_SERVICE) .
            $this->getScopeConfigValues(self::CONFIG_PATH_CN_SERVICE_VERSION);

        return $url . $dir . $service;
    }

    /**
     * Get HistoricalData
     * @return string
     */
    public function getHistoricalData()
    {
        if ($this->getScopeConfigValues(self::CONFIG_PATH_HISTORYCAL_DATA)) {
            return "y";
        } else {
            return "n";
        }
    }

    /**
     * Get TestString
     * @return string
     */
    public function getTestString()
    {
        return $this->getScopeConfigValues(self::CONFIG_PATH_TEST_STRING);
    }

    /**
     * Get ScopeConfigValues
     *
     * @param $path
     *
     * @return string
     */
    private function getScopeConfigValues($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BaseUrl
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @param $request
     *
     * @return string
     */
    public function buildQuery($request)
    {
        return http_build_query($request);
    }
}
