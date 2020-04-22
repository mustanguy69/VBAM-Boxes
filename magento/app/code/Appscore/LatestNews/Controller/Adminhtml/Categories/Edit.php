<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class Edit extends Categories
{
   /**
     * @return void
     */
   public function execute()
   {
        $categoryId = $this->getRequest()->getParam('id');
        $model = $this->_categoriesFactory->create();

        if ($categoryId) {
            $model->load($categoryId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This category no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('latestnews_categories', $model);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Appscore_LatestNews::latestnews');
        $resultPage->getConfig()->getTitle()->prepend(__('Category Latest News'));

        return $resultPage;
   }
}