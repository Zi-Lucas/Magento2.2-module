<?php
namespace Aosom\Marketing\Controller\Adminhtml\Trial;

class Invite extends \Aosom\Marketing\Controller\Adminhtml\Trial
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

            $customerId = $model->getCustomerId();
            if (!empty($customerId)){
                $customerRepository = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Customer\Api\CustomerRepositoryInterface::class);
                $customer = $customerRepository->getById($customerId);
                $to = $customer->getEmail();
                if (!empty($to)){
                    $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Aosom\Marketing\Helper\Data::class);
                    $params['report_id'] = $model->getId();
                    if ($helper->sendEmail('',$to,'',$helper->trialEmailTemplete('invite'),$params)){
                        $this->messageManager->addSuccess(__('Send Invite Trial Report Email Success.'));
                        $model->setStatus(2)->save();
                        $this->_redirect('aosom_marketing/trial/index');
                        return;
                    }else{
                        $this->messageManager->addErrorMessage(__('can not be send email.'));
                        $this->_redirect('aosom_marketing/trial/index');
                        return;
                    }
                }else{
                    $this->messageManager->addErrorMessage(__('customer email is null, can not be send email.'));
                    $this->_redirect('aosom_marketing/trial/index');
                    return;
                }
            }else{
                $this->messageManager->addErrorMessage(__('customer no longer exists, can not be send email.'));
                $this->_redirect('aosom_marketing/trial/index');
                return;
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aosom_Marketing::trial_invite');
    }
}
