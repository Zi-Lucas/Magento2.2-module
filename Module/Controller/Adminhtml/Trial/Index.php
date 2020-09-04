<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aosom\Marketing\Controller\Adminhtml\Trial;

use Aosom\Marketing\Helper\Data;
class Index extends \Aosom\Marketing\Controller\Adminhtml\Trial
{
    public function execute()
    {
//        $contactConfig = $this->_objectManager->create(\Aosom\Marketing\Helper\Data::class);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aosom_Marketing::amarketing_trial');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Trial Report'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aosom_Marketing::amarketing_trial');
    }
}
