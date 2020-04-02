<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

class Edit extends Branchlist
{
   /**
     * @return void
     */
   public function execute()
   {
        $branchId = $this->getRequest()->getParam('id');
        $model = $this->_branchlistFactory->create();

        if ($branchId) {
            $model->load($branchId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This branch no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getBranchData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('branchlocator_branchlist', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Appscore_BranchLocator::branchlocator');
        $resultPage->getConfig()->getTitle()->prepend(__('Branch'));

        return $resultPage;
   }
}