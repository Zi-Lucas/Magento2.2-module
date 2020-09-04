<?php

namespace Aosom\Marketing\Block\Adminhtml;

class Testreport extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_testreport';
        $this->_blockGroup = 'Aosom_Marketing';
        $this->_headerText = __('Manage Testreport');
        $this->_addButtonLabel = __('Add New Testreport');
        parent::_construct();
    }
}
