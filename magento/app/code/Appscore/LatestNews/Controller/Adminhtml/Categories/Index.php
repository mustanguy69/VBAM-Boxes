<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class Index extends Categories
{
    /**
     * @return void
     */
   public function execute()
   {
      if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Appscore_LatestNews::latestnews');
        $resultPage->getConfig()->getTitle()->prepend(__('Latest News Categories List'));

        return $resultPage;
   }
}