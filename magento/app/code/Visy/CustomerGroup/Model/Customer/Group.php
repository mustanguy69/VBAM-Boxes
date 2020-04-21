<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Model\Customer;

use Exception;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Visy\CustomerGroup\Model\ResourceModel\GroupMapping\CollectionFactory as GroupMappingCollection;
use Psr\Log\LoggerInterface;

class Group
{

    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var GroupMappingCollection
     */
    protected $groupMappingCollectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Group constructor.
     * @param SessionManagerInterface $coreSession
     * @param StoreManagerInterface $storeManager
     * @param GroupMappingCollection $groupMappingCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SessionManagerInterface $coreSession,
        StoreManagerInterface $storeManager,
        GroupMappingCollection $groupMappingCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->_coreSession = $coreSession;
        $this->groupMappingCollectionFactory = $groupMappingCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function fetchRegionGroupId($existingCustomerGroupId)
    {
        try {
            $cookieName = 'regionId';
            //TODO: Temp patch - This needs to be optimized before pushing LIVE
            //if ($this->getRegionGroupId() == null) {
            $storeId = $this->getStoreId(); //1
            $regionIdCookie = 5443;
            if(isset($_COOKIE[$cookieName])) {
                $regionIdCookie = $_COOKIE[$cookieName];
            }
            $regionId = $regionIdCookie;
            $groupMappingCollection = $this->groupMappingCollectionFactory->create();
            $groupMappingCollection->addFieldToFilter('store_id', ['eq' => $storeId]);
            $groupMappingCollection->addFieldToFilter('region_id', ['eq' => $regionId]);

            if ($groupMappingCollection->count() == 1) {
                $data = $groupMappingCollection->getData()[0];
                $groupId = $data['customer_group_id'];
                $customerCode = $data['customer_code'];
                $this->setRegionGroupId($groupId);
                $this->setCustomerCode($customerCode);
                return $groupId;
            } else {
                $this->unsetRegionGroupId();
                $this->unsetCustomerCode();
                return $existingCustomerGroupId;
            }
            /*} else {
                return $this->getRegionGroupId();
            }*/
        } catch (Exception $e) {
            $this->logger->error($e);
            return $existingCustomerGroupId;
        }
    }

    /**
     * Set CustomerCode
     * @param $customerCode
     */
    public function setCustomerCode($customerCode)
    {
        $this->_coreSession->start();
        $this->_coreSession->setCustomerCode($customerCode);
    }

    /**
     * Get CustomerCode
     * @return mixed
     */
    public function getCustomerCode()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getCustomerCode();
    }

    /**
     * Unset CustomerCode
     * @return mixed
     */
    public function unsetCustomerCode()
    {
        $this->_coreSession->start();
        return $this->_coreSession->unsCustomerCode();
    }

    /**
     * Set RegionGroupId
     * @param $regionGroupId
     */
    public function setRegionGroupId($regionGroupId)
    {
        $this->_coreSession->start();
        $this->_coreSession->setRegionGroupId($regionGroupId);
    }

    /**
     * Get RegionGroupId
     * @return mixed
     */
    public function getRegionGroupId()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getRegionGroupId();
    }

    /**
     * Unset RegionGroupId
     * @return mixed
     */
    public function unsetRegionGroupId()
    {
        $this->_coreSession->start();
        return $this->_coreSession->unsRegionGroupId();
    }

    /**
     * Set DefaultGroupId
     * @param $defaultGroupId
     */
    public function setDefaultGroupId($defaultGroupId)
    {
        $this->_coreSession->start();
        $this->_coreSession->setDefaultGroupId($defaultGroupId);
    }

    /**
     * Get DefaultGroupId
     * @return mixed
     */
    public function getDefaultGroupId()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getDefaultGroupId();
    }

    /**
     * Unset DefaultGroupId
     * @return mixed
     */
    public function unsetDefaultGroupId()
    {
        $this->_coreSession->start();
        return $this->_coreSession->unsDefaultGroupId();
    }

    /**
     * Get store identifier
     *
     * @return  int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
