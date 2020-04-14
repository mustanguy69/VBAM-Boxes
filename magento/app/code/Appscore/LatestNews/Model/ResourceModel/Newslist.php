<?php

namespace Appscore\LatestNews\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Newslist extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('appscore_latestnews_news', 'id');
    }
}