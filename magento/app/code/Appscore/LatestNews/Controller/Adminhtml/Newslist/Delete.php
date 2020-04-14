<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Newslist;

use Appscore\LatestNews\Controller\Adminhtml\Newslist;

class Delete extends Newslist
{
   /**
    * @return void
    */
   public function execute()
   {
      $newsId = (int) $this->getRequest()->getParam('id');

      if ($newsId) {
         $newsModel = $this->_newslistFactory->create();
         $newsModel->load($newsId);

         if (!$newsModel->getId()) {
            $this->messageManager->addError(__('This news no longer exists.'));
         } else {
               try {
                  $newsModel->delete();
                  $this->messageManager->addSuccess(__('The news has been deleted.'));

                  $this->_redirect('*/*/');
                  return;
               } catch (\Exception $e) {
                   $this->messageManager->addError($e->getMessage());
                   $this->_redirect('*/*/edit', ['id' => $newsModel->getId()]);
               }
            }
      }
   }
}