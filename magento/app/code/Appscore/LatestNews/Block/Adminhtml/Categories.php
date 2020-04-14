<?php

namespace Appscore\LatestNews\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Categories extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_categories';
        $this->_blockGroup = 'Appscore_LatestNews';
        $this->_headerText = __('Manage Categories');
        $this->_addButtonLabel = __('Add Category');
        parent::_construct();
    }
}