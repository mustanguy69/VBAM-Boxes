<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class MassDelete extends Categories
{
   /**
    * @return void
    */
   public function execute()
   {
      $categoryIds = $this->getRequest()->getParam('category');

        foreach ($categoryIds as $categoryId) {
            try {
                $categoryModel = $this->_categoriesFactory->create();
                $categoryModel->load($categoryId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($categoryIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($categoryIds))
            );
        }

        $this->_redirect('*/*/index');
   }
}