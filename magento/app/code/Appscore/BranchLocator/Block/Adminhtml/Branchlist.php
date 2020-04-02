<?php

namespace Appscore\BranchLocator\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Branchlist extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_branchlist';
        $this->_blockGroup = 'Appscore_BranchLocator';
        $this->_headerText = __('Manage Branch');
        $this->_addButtonLabel = __('Add Branch');
        parent::_construct();
    }
}
