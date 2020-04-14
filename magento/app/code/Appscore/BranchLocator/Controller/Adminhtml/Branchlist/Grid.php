<?php

namespace Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

use Appscore\BranchLocator\Controller\Adminhtml\Branchlist;

class Grid extends Branchlist
{
   /**
     * @return void
     */
   public function execute()
   {
      return $this->_resultPageFactory->create();
   }
}