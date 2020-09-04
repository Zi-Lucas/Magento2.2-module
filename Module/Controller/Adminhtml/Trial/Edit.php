<?php
namespace Aosom\Marketing\Controller\Adminhtml\Trial;

class Edit extends \Aosom\Marketing\Controller\Adminhtml\Trial
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('trial_id');
        $model = $this->_objectManager->create('Aosom\Marketing\Model\Trial');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Trial no longer exists.'));
                $this->_redirect('aosom_marketing/trial/index');
                return;
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_trial', $model);
        $this->_initAction()->_addBreadcrumb(
            $id ? __('Edit Trial') : __('Add New Trial'),
            $id ? __('Edit Trial') : __('Add New Trial')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Trial Report'));
        $this->_view->getLayout()->getBlock('trial_edit');
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aosom_Marketing::trial_edit');
    }
}
