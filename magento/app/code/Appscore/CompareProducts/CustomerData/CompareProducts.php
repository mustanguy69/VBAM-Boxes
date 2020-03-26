<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appscore\CompareProducts\CustomerData;

class CompareProducts extends \Magento\Catalog\CustomerData\CompareProducts
{
    public function getSectionData()
    {
        $count = $this->helper->getItemCount();
        return [
            'count' => $count,
            'countCaption' => $count == 1 ? __('1 product') : __('%1 products', $count),
            'listUrl' => $this->helper->getListUrl(),
            'items' => $count ? $this->getItems() : [],
        ];
    }
}
