<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class Save extends Categories
{

   /**
     * @return void
     */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();

      if ($isPost) {
         $categoryModel = $this->_categoriesFactory->create();

         if (array_key_exists('id', $this->getRequest()->getParam('category'))) {
            $categoryModel->load($this->getRequest()->getParam('category')['id']);
         }

         $formData = $this->getRequest()->getParam('category');
         $categoryModel->setData($formData);
      
         try {
            $categoryModel->save();

            // Display success message
            $this->messageManager->addSuccess(__('The category has been saved.'));

            // Check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
               $this->_redirect('*/*/edit', ['id' => $categoryModel->getId(), '_current' => true]);
               return;
            }

            // Go to grid page
            $this->_redirect('*/*/');
            return;
         } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
         }

         $this->_getSession()->setFormData($formData);
         $this->_redirect('*/*/edit', ['id' => $categoryModel->getId()]);
      }
   }
}