<?php

namespace Appscore\LatestNews\Model\ResourceModel\Categories;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Appscore\LatestNews\Model\Categories',
            'Appscore\LatestNews\Model\ResourceModel\Categories'
        );
    }
}