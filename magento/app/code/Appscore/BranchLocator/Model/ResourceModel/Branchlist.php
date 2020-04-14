<?php

namespace Appscore\BranchLocator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Branchlist extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('appscore_branchlocator_branchlist', 'id');
    }
}