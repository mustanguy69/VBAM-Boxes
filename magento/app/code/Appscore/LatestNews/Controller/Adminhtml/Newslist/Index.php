<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Newslist;

use Appscore\LatestNews\Controller\Adminhtml\Newslist;

class Index extends Newslist
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
        $resultPage->getConfig()->getTitle()->prepend(__('Latest News List'));

        return $resultPage;
   }
}