<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

class MassDelete extends Branchlist
{
   /**
    * @return void
    */
   public function execute()
   {
      $branchIds = $this->getRequest()->getParam('branch');

        foreach ($branchIds as $branchId) {
            try {
                $branchModel = $this->_branchlistFactory->create();
                $branchModel->load($branchId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($branchIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($branchIds))
            );
        }

        $this->_redirect('*/*/index');
   }
}