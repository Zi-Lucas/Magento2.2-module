<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aosom\Marketing\Controller\Adminhtml\Trial;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Preview extends \Aosom\Marketing\Controller\Adminhtml\Trial
{

    public function execute()
    {
        try {
            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Trial Report Preview'));
            $this->_view->renderLayout();
            $this->getResponse()->setHeader('Content-Security-Policy', "script-src 'none'");
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred. The Trial Report can not be opened for preview.'));
            $this->_redirect('adminhtml/*/');
        }
    }
}
