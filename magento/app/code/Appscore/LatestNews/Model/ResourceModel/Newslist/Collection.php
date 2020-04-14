<?php

namespace Appscore\LatestNews\Model\ResourceModel\Newslist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Appscore\LatestNews\Model\Newslist',
            'Appscore\LatestNews\Model\ResourceModel\Newslist'
        );
    }
}