<?php

namespace Appscore\BranchLocator\Model;

use Magento\Framework\Model\AbstractModel;

class Branchlist extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Appscore\BranchLocator\Model\ResourceModel\Branchlist');
    }
}