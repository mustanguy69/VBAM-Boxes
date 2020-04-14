<?php

namespace Appscore\BranchLocator\Block\Adminhtml\Branchlist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Hours extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $newsStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('branchlocator_branchlist');
    
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('branch_');
        $form->setFieldNameSuffix('branch');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Opening Hours')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }


        $fieldset->addField(
            'monday',
            'text',
            [
                'name'        => 'monday',
                'label'    => __('Monday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
            ]
        );

        $fieldset->addField(
            'tuesday',
            'text',
            [
                'name'        => 'tuesday',
                'label'    => __('Tuesday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
            ]
        );

        $fieldset->addField(
            'wednesday',
            'text',
            [
                'name'        => 'wednesday',
                'label'    => __('Wednesday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
            ]
        );

        $fieldset->addField(
            'thursday',
            'text',
            [
                'name'        => 'thursday',
                'label'    => __('Thursday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
            ]
        );

        $fieldset->addField(
            'friday',
            'text',
            [
                'name'        => 'friday',
                'label'    => __('Friday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
            ]
        );

        $fieldset->addField(
            'saturday',
            'text',
            [
                'name'        => 'saturday',
                'label'    => __('Saturday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
            ]
        );

        $fieldset->addField(
            'sunday',
            'text',
            [
                'name'        => 'sunday',
                'label'    => __('Sunday'),
                'required'     => true,
                'placeholder' => 'Format: 9:00am-5:30pm or Closed'
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
        return __('Branch Opening hours');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Branch Opening hours');
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