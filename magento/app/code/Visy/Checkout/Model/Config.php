<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Checkout\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State as AppState;
use Magento\Sales\Model\AdminOrder\Create as AdminOrderCreate;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const XPATH_DISABLE_DELIVERY_DATE  = 'visy_checkout/general/disable_delivery_date';
    const XPATH_FORMAT                 = 'visy_checkout/general/format';
    const XPATH_DISABLED               = 'visy_checkout/general/disabled';
    const XPATH_ENABLE_DELIVERY_TIME   = 'visy_checkout/general/enable_delivery_time';
    const XPATH_HOURMIN                = 'visy_checkout/general/hourMin';
    const XPATH_HOURMAX                = 'visy_checkout/general/hourMax';
    const XPATH_REQUIRED_DELIVERY_DATE = 'visy_checkout/general/required_delivery_date';
    const XPATH_DELIVERY_DAYS_AHEAD    = 'visy_checkout/general/minDate';

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var AdminOrderCreate
     */
    protected $adminOrderCreate;

    /**
     * Config constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param AppState $appState
     * @param AdminOrderCreate $adminOrderCreate
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        AppState $appState,
        AdminOrderCreate $adminOrderCreate
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->appState = $appState;
        $this->adminOrderCreate = $adminOrderCreate;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormat()
    {
        $store = $this->getStoreId();

        return $this->scopeConfig->getValue(self::XPATH_FORMAT, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDisabled()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_DISABLED, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHourMin()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_HOURMIN, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHourMax()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_HOURMAX, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRequiredDeliveryDate()
    {
        $store = $this->getStoreId();
        return (bool) $this->scopeConfig->getValue(self::XPATH_REQUIRED_DELIVERY_DATE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEnableDeliveryTime()
    {
        $store = $this->getStoreId();
        return (bool) $this->scopeConfig->getValue(self::XPATH_ENABLE_DELIVERY_TIME, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDisableDeliveryDate()
    {
        $store = $this->getStoreId();
        return (bool) $this->scopeConfig->getValue(self::XPATH_DISABLE_DELIVERY_DATE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDeliveryDaysAhead()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_DELIVERY_DAYS_AHEAD, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        if (!$this->storeId) {
            if ($this->appState->getAreaCode() == 'adminhtml') {
                $this->storeId = $this->adminOrderCreate->getQuote()->getStoreId();
            } else {
                $this->storeId = $this->storeManager->getStore()->getStoreId();
            }
        }

        return $this->storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $disabledDeliveryDate = $this->getDisableDeliveryDate();
        $disabled = $this->getDisabled();
        $hourMin = $this->getHourMin();
        $hourMax = $this->getHourMax();
        $format = $this->getFormat();
        $showTimepicker = $this->getEnableDeliveryTime();
        $deliveryDaysAhead = $this->getDeliveryDaysAhead();

        $noday = 0;
        if ($disabled == -1) {
            $noday = 1;
        }

        $config = [
            'shipping' => [
                'delivery_date' => [
                    'disabledDeliveryDate' => $disabledDeliveryDate,
                    'format' => $format,
                    'disabled' => $disabled,
                    'noday' => $noday,
                    'showTimepicker' => $showTimepicker,
                    'hourMin' => $hourMin,
                    'hourMax' => $hourMax,
                    'minDate' => $deliveryDaysAhead
                ]
            ]
        ];

        return $config;
    }
}
