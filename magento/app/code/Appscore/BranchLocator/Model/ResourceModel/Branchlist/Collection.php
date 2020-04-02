<?php

namespace Appscore\BranchLocator\Model\ResourceModel\Branchlist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Appscore\BranchLocator\Model\Branchlist',
            'Appscore\BranchLocator\Model\ResourceModel\Branchlist'
        );
    }
}