<?php

namespace Aosom\Marketing\Block\Adminhtml;

class Trial extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_trial';
        $this->_blockGroup = 'Aosom_Marketing';
        $this->_headerText = __('Manage Trial');
        $this->_addButtonLabel = __('Add New Trial');
        parent::_construct();
    }
}
