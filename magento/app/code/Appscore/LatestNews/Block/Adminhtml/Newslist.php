<?php

namespace Appscore\LatestNews\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Newslist extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_newslist';
        $this->_blockGroup = 'Appscore_LatestNews';
        $this->_headerText = __('Manage News');
        $this->_addButtonLabel = __('Add News');
        parent::_construct();
    }
}