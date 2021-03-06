<?php

namespace Appscore\LatestNews\Block\Adminhtml\Categories\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('categories_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Categories'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'news_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Appscore\LatestNews\Block\Adminhtml\Categories\Edit\Tab\Content'
                )->toHtml(),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}