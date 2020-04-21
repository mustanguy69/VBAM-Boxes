<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

class NewAction extends Branchlist
{
   public function execute()
   {
      $this->_forward('edit');
   }
}