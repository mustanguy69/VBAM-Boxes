<?php


namespace Appscore\CustomerCustomAttributes\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Visy\CustomerGroup\Model\Block\Source\RegionData;

/**
 * Class CatalogWidget
 *
 * @package Appscore\SliderWidget\Block\Widget
 */
class StateLocation extends Template implements BlockInterface
{

    protected $_template = "statelocation.phtml";

    protected $_storeManager;

    protected $_regionData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RegionData $regionData,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_regionData = $regionData;
        parent::__construct($context, $data);
    }

    public function getStateList()
    {
        $stateArray = $this->_regionData->getRegions();

        $stateList = [];

        foreach($stateArray as $state) {            
            if(isset($state['label']) && trim($state['label']) != '') {
                $stateList[$state['label']] = $state['value'];
            }
        }

        return $stateList;
    }

    public function toHtml($className = 'desktop') {
        $stateArray = $this->getStateList();
        $html = '';

        if(count($stateArray) > 0) {
            $html .= '<div class="current_region '.$className.'"><select name="current_region" title="State" defaultvalue="0">';
                foreach ($stateArray as $key => $value) {
                    $html .= '<option value="'.$value.'">'.$key.'</option>';
                }
            $html .= '</select></div>';
                    
        }

        return $html;
    }
}

