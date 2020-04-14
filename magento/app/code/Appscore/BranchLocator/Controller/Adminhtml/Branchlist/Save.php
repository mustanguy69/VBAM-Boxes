<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

class Save extends Branchlist
{
   /**
     * @return void
     */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();

      if ($isPost) {
         $branchModel = $this->_branchlistFactory->create();
         
         if (array_key_exists('id', $this->getRequest()->getParam('branch'))) {
            $branchModel->load($this->getRequest()->getParam('branch')['id']);
         }

         $formData = $this->getRequest()->getParam('branch');
         $branchModel->setData($formData);
         
         try {
            // Save news
            $branchModel->save();

            // Display success message
            $this->messageManager->addSuccess(__('The branch has been saved.'));

            // Check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
               $this->_redirect('*/*/edit', ['id' => $branchModel->getId(), '_current' => true]);
               return;
            }

            // Go to grid page
            $this->_redirect('*/*/');
            return;
         } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
         }

         $this->_getSession()->setFormData($formData);
         $this->_redirect('*/*/edit', ['id' => $branchModel->getId()]);
      }
   }
}
