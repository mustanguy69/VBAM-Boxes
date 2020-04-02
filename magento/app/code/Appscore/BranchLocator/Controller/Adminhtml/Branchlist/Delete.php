<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

class Delete extends Branchlist
{
   /**
    * @return void
    */
   public function execute()
   {
      $branchId = (int) $this->getRequest()->getParam('id');

      if ($branchId) {
         /** @var $newsModel \Mageworld\SimpleNews\Model\News */
         $branchModel = $this->_branchlistFactory->create();
         $branchModel->load($branchId);

         // Check this news exists or not
         if (!$branchModel->getId()) {
            $this->messageManager->addError(__('This branch no longer exists.'));
         } else {
               try {
                  // Delete news
                  $branchModel->delete();
                  $this->messageManager->addSuccess(__('The branch has been deleted.'));

                  // Redirect to grid page
                  $this->_redirect('*/*/');
                  return;
               } catch (\Exception $e) {
                   $this->messageManager->addError($e->getMessage());
                   $this->_redirect('*/*/edit', ['id' => $branchModel->getId()]);
               }
            }
      }
   }
}