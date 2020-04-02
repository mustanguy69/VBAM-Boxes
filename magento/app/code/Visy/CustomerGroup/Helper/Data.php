<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Visy\CustomerGroup\Model\ResourceModel\GroupMapping\CollectionFactory as GroupMappingCollection;

/**
 * Class Data
 * @package Visy\CustomerGroup\Helper
 */
class Data extends AbstractHelper
{
    protected $groupMappingCollectionFactory;

    public function __construct(
        Context $context,
        GroupMappingCollection $groupMappingCollectionFactory
    ) {
        parent::__construct($context);
        $this->groupMappingCollectionFactory =   $groupMappingCollectionFactory;
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMappingByRegionAndStore(
        $storeId,
        $regionId,
        $defaultCustomerGroup,
        $defaultSapCustomerCode = null
    ) {
        $groupMappingCollection = $this->groupMappingCollectionFactory->create();
        $groupMappingCollection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $groupMappingCollection->addFieldToFilter('region_id', ['eq' => $regionId]);

        if (!empty($groupMappingData = $groupMappingCollection->getData()[0])) {
            return [
                'customer_group'=>$groupMappingData['customer_group_id'],
                'customer_code'=>$groupMappingData['customer_code'],
            ];
        } else {
            return [
                'customer_group'=>$defaultCustomerGroup,
                'customer_code'=>$defaultSapCustomerCode,
            ];
        }
    }
}
