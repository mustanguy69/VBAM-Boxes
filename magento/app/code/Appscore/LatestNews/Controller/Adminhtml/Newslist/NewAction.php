<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Newslist;

use Appscore\LatestNews\Controller\Adminhtml\Newslist;

class NewAction extends Newslist
{
   /**
     *
     * @return void
     */
   public function execute()
   {
      $this->_forward('edit');
   }
}