<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aosom\Marketing\Controller\Adminhtml\Testreport;

use Aosom\Marketing\Helper\Data;
class Index extends \Aosom\Marketing\Controller\Adminhtml\Testreport
{
    public function execute()
    {
//        $contactConfig = $this->_objectManager->create(\Aosom\Marketing\Helper\Data::class);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aosom_Marketing::amarketing_testreport');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Customer experience test report'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aosom_Marketing::amarketing_testreport');
    }
}
