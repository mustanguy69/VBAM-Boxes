<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\Branchlocator\Controller\Adminhtml\Branchlist;

class NewAction extends Branchlist
{
   /**
     * Create new news action
     *
     * @return void
     */
   public function execute()
   {
      $this->_forward('edit');
   }
}