<?php

namespace Appscore\BranchLocator\Block\Adminhtml\Branchlist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Appscore\BranchLocator\Model\System\Config\Status;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \BranchLocator\Model\System\Config\Status
     */
    protected $_branchlistStatus;

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
        Status $branchlistStatus,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_branchlistStatus = $branchlistStatus;
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
                'options'   => $this->_branchlistStatus->toOptionArray()
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'        => 'name',
                'label'    => __('Name'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'name'        => 'address',
                'label'    => __('Address'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'        => 'city',
                'label'    => __('City'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'state',
            'text',
            [
                'name'        => 'state',
                'label'    => __('State'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name'        => 'postcode',
                'label'    => __('Postcode'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name'        => 'phone',
                'label'    => __('Phone'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name'        => 'fax',
                'label'    => __('Fax'),
                'required'     => false
            ]
        );

        $fieldset->addField(
            'opening_hours',
            'text',
            [
                'name'        => 'opening_hours',
                'label'    => __('Opening Hours'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'latitude',
            'text',
            [
                'name'        => 'latitude',
                'label'    => __('Latitude'),
                'required'     => false
            ]
        );

        $fieldset->addField(
            'longitude',
            'text',
            [
                'name'        => 'longitude',
                'label'    => __('Longitude'),
                'required'     => false
            ]
        );

        

        $data = $model->getData();
        $form->setValues($data);
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
        return __('Branch Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Branch Info');
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