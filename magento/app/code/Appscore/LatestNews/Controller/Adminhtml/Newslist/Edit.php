<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Newslist;

use Appscore\LatestNews\Controller\Adminhtml\Newslist;

class Edit extends Newslist
{
   /**
     * @return void
     */
   public function execute()
   {
        $newsId = $this->getRequest()->getParam('id');
        $model = $this->_newslistFactory->create();

        if ($newsId) {
            $model->load($newsId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('latestnews_newslist', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Appscore_LatestNews::latestnews');
        $resultPage->getConfig()->getTitle()->prepend(__('Latest News'));

        return $resultPage;
   }
}