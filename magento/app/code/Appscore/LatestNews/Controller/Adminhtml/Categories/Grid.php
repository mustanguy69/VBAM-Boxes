<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class Grid extends Categories
{
   /**
     * @return void
     */
   public function execute()
   {
      return $this->_resultPageFactory->create();
   }
}