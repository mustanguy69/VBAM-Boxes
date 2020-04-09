<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Store\Model\StoreManagerInterface;
use Visy\CustomerGroup\Helper\Data as VisyCustomerGroupHelper;

class UpdateCustomerGroupAndSapCode implements ObserverInterface
{
    protected $_customerRepositoryInterface;
    protected $_customerSession;
    protected $_helper;
    protected $_storeManager;

    public function __construct(
        RequestInterface $request,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        VisyCustomerGroupHelper $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
    }

    public function execute(EventObserver $observer)
    {
        $customerId = $observer->getEvent()->getCustomer()->getId();
        $customer = $this->_customerRepositoryInterface->getById($customerId);

        //$defaultGroupId = $this->getCurrentGroupId();
        $storeId = $this->getStoreId();
        $defaultGroupId = $customer->getGroupId();
        $regionId = $customer->getCustomAttribute('region')->getValue();
        $regionalData = $this->_helper->getMappingByRegionAndStore($storeId, $regionId, $defaultGroupId);
        $customerGroupId = $regionalData['customer_group'];
        $customerCode = $regionalData['customer_code'];
        $customer->setGroupId($customerGroupId);
        $customer->setCustomAttribute('customer_code', $customerCode);

        try {
            $this->_customerRepositoryInterface->save($customer);
        } catch (InputException $e) {
        } catch (InputMismatchException $e) {
        } catch (LocalizedException $e) {
        }
    }

    public function getCurrentGroupId()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getGroupId();
        } else {
            return 0;
        }
    }

    /**
     * Get store identifier
     *
     * @return  int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get website identifier
     *
     * @return string|int|null
     * @throws NoSuchEntityException
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Get Store code
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * Get Store name
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
}
