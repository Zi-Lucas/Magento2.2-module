<?php
namespace Aosom\Marketing\Controller\Adminhtml\Trial;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Aosom\Marketing\Controller\Adminhtml\Trial
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('trial_id');
            $model = $this->_objectManager->create('Aosom\Marketing\Model\Trial')->load($id);

            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            unset($data['imgs']);
//            $imgs = [];
//            foreach ($data['imgs'] as $img){
//                $imgs[] = $img['value'];
//            }
//            $data['imgs'] = serialize($imgs);
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the item.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['trial_id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('trial_id');
                if (!empty($id)) {
                    $this->_redirect('aosom_marketing/trial/edit', ['trial_id' => $id]);
                } else {
                    $this->_redirect('aosom_marketing/trial/index');
                }
                return;
            }catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['trial_id' => $this->getRequest()->getParam('trial_id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aosom_Marketing::trial_save');
    }
}
