<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Categories;

use Appscore\LatestNews\Controller\Adminhtml\Categories;

class NewAction extends Categories
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