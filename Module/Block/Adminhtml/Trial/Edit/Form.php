<?php
namespace Aosom\Marketing\Block\Adminhtml\Trial\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('trial_form');
        $this->setTitle(__('Trial Information'));
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('aosom_marketing/trial/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
