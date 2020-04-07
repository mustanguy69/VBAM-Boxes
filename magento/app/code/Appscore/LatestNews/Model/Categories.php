<?php

namespace Appscore\LatestNews\Model;

use Magento\Framework\Model\AbstractModel;

class Categories extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Appscore\LatestNews\Model\ResourceModel\Categories');
    }
}