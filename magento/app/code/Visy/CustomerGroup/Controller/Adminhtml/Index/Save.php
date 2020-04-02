<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Visy\CustomerGroup\Model\GroupMappingFactory;
use Visy\CustomerGroup\Model\ResourceModel\GroupMapping\CollectionFactory as GroupMappingCollection;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Index';

    protected $resultPageFactory;
    protected $groupMappingFactory;
    protected $groupMappingCollectionFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GroupMappingFactory $groupMappingFactory,
        GroupMappingCollection $groupMappingCollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->groupMappingFactory = $groupMappingFactory;
        $this->groupMappingCollectionFactory = $groupMappingCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        $storeId = $data['store_id'];

        if ($data) {
            try {
                $id = $data['visy_customergroup_map_id'];
                $groupMappingFactory = $this->groupMappingFactory->create();
                $groupMapping = $groupMappingFactory->load($id);

                $groupMappingCollection = $this->groupMappingCollectionFactory->create();
                $groupMappingCollection->addFieldToFilter('store_id', ['eq' => $data['store_id']]);
                $groupMappingCollection->addFieldToFilter('region_id', ['eq' => $data['region_id']]);
                $count = $groupMappingCollection->count();
                if ($count > 0 && $groupMappingCollection->getData()[0]['visy_customergroup_map_id'] != $id) {
                    throw new \Exception('Selected Store & Region Customer Group mapping is already exist. Please check again!');
                } else {
                    $data = array_filter($data, function ($value) {
                        return $value !== '';
                    });

                    $groupMapping->setData($data);
                    $groupMapping->setStoreId($storeId[0]);

                    $groupMapping->save();
                    $this->messageManager->addSuccess(__('Successfully saved the mapping.'));

                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $groupMapping->getId()]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
