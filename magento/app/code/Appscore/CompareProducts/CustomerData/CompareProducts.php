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
        $textCount = __('0 product');

        if($count != 0) {
            if($count == 1) {
                $textCount = __('1 product');
            } else {
                $textCount = __('%1 products', $count);
            }
        }
        
        return [
            'count' => $count,
            'countCaption' => $textCount,
            'listUrl' => $this->helper->getListUrl(),
            'items' => $count ? $this->getItems() : [],
        ];
    }
}
