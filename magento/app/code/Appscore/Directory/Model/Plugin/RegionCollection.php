<?php
namespace Appscore\Directory\Model\Plugin;

use Magento\Directory\Model\ResourceModel\Region\Collection as OriginalRegionsCollection;

class RegionCollection
{
    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function afterToOptionArray($subject, $options) 
    {

        $options[0] = ['title' => '', 'value' => '', 'label' => __('Select'), '' => 'disabled'];

        return $options;
    }
}