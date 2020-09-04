<?php
namespace Aosom\Marketing\Controller\Adminhtml\Trial;

class NewAction extends \Aosom\Marketing\Controller\Adminhtml\Trial
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aosom_Marketing::trial_edit');
    }
}
