<?php
namespace Appscore\Directory\Model\Plugin;

use Magento\Directory\Model\ResourceModel\Country\Collection as OriginalCountrysCollection;

class CountryCollection
{
    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function afterToOptionArray($subject, $options)
    {
        
        $options[0] = ['title' => '', 'value' => '', 'label' => __('Select'), 'disabled' => 'disabled'];

        return $options;
    }
}