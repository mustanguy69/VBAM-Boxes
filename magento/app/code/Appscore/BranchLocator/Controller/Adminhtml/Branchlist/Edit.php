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

        $data = $this->_session->getBranchData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('branchlocator_branchlist', $model);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Appscore_BranchLocator::branchlocator');
        $resultPage->getConfig()->getTitle()->prepend(__('Branch'));

        return $resultPage;
   }
}