<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Newslist;

use Appscore\LatestNews\Controller\Adminhtml\Newslist;

class MassDelete extends Newslist
{
   /**
    * @return void
    */
   public function execute()
   {
      $newsIds = $this->getRequest()->getParam('news');

        foreach ($newsIds as $newsId) {
            try {
                $newsModel = $this->_newslistFactory->create();
                $newsModel->load($newsId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($newsIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($newsIds))
            );
        }

        $this->_redirect('*/*/index');
   }
}