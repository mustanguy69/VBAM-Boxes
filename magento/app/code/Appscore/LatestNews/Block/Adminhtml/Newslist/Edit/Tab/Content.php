<?php

namespace Appscore\LatestNews\Block\Adminhtml\Newslist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Appscore\LatestNews\Model\System\Config\Status;
use Appscore\LatestNews\Model\System\Config\Categories;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Content extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \LatestNews\Model\System\Config\Status
     */
    protected $_newslistStatus;

    /**
     * @var \LatestNews\Model\System\Config\Categories
     */
    protected $_categories;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $newsStatus
     * @param Categories $categories
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $newslistStatus,
        Categories $categories,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_newslistStatus = $newslistStatus;
        $this->_categories = $categories;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('latestnews_newslist');
    
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('news_');
        $form->setFieldNameSuffix('news');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'name'      => 'status',
                'label'     => __('Status'),
                'options'   => $this->_newslistStatus->toOptionArray()
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name'        => 'title',
                'label'    => __('Title'),
                'required'     => true
            ]
        );
        
        $fieldset->addField(
            'category_id',
            'select',
            [
                'name'        => 'category_id',
                'label'    => __('Category'),
                'required'     => true,
                'values'   => $this->_categories->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name'        => 'image',
                'label'    => __('Image'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'content',
            'editor',
            [
                'name'        => 'content',
                'label'    => __('Content'),
                'required'     => true,
                'config'    => $this->_wysiwygConfig->getConfig(),
                'wysiwyg'   => true
            ]
        );

        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('News Content');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('News Content');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}