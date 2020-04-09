<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Model\Block\Source;

use Magento\Directory\Model\RegionFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class RegionData
 */
class RegionData implements OptionSourceInterface
{
    /**
     * @var RegionFactory
     */
    protected $regionColFactory;

    protected $eavConfig;

    /**
     * RegionData constructor.
     * @param RegionFactory $regionColFactory
     * @param EavConfig $eavConfig
     * @param array $data
     */
    public function __construct(
        RegionFactory $regionColFactory,
        EavConfig $eavConfig,
        array $data = []
    ) {
        $this->regionColFactory     = $regionColFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Get options
     *
     * @return array
     */
    /*public function toOptionArray()
    {
        $regions = $this->getAuRegions();
        $options = [];
        foreach ($regions as $key => $value) {
            $options[] = [
                'label' => $value->getDefaultName(), //getCode()
                'value' => $key,
            ];
        }
        return $options;
    }*/

    public function toOptionArray()
    {
        $regions = $this->getRegions();
        $options = [];
        foreach ($regions as $key => $value) {
            $options[] = [
                'label' => $value['label'],
                'value' => $value['value'],
            ];
        }
        return $options;
    }

    public function getAuRegions()
    {
        $regions = $this->regionColFactory->create()->getCollection()->
        addFieldToFilter('country_id', 'AU');
        return $regions;
    }

    public function getRegions()
    {
        $attribute = $this->eavConfig->getAttribute("customer", 'region');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }
}
