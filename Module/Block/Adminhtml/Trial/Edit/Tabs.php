<?php
namespace Aosom\Marketing\Block\Adminhtml\Trial\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('trial_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Trial'));
    }
}
