<?php

namespace Appscore\LatestNews\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Categories extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('appscore_latestnews_categories', 'id');
    }
}