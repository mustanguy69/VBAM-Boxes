<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Model\ResourceModel\GroupMapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'visy_customergroup_map_id';

    protected function _construct()
    {
        $this->_init(
            'Visy\CustomerGroup\Model\GroupMapping',
            'Visy\CustomerGroup\Model\ResourceModel\GroupMapping'
        );
    }

    public function setRegionFilter($regionId)
    {
        $this->addFieldToFilter('region_id', (int)$regionId);
        return $this;
    }

    public function setStoreFilter($storeId)
    {
        $this->addFieldToFilter('store_id', (int)$storeId);
        return $this;
    }
}
