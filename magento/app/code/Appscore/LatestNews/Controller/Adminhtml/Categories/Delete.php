<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class Delete extends Categories
{
   /**
    * @return void
    */
   public function execute()
   {
      $categoryId = (int) $this->getRequest()->getParam('id');

      if ($categoryId) {
         $categoryModel = $this->_categoriesFactory->create();
         $categoryModel->load($categoryId);

         if (!$categoryModel->getId()) {
            $this->messageManager->addError(__('This category no longer exists.'));
         } else {
               try {
                  $categoryModel->delete();
                  $this->messageManager->addSuccess(__('The category has been deleted.'));

                  $this->_redirect('*/*/');
                  return;
               } catch (\Exception $e) {
                   $this->messageManager->addError($e->getMessage());
                   $this->_redirect('*/*/edit', ['id' => $categoryModel->getId()]);
               }
            }
      }
   }
}