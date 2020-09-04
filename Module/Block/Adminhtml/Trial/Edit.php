<?php
namespace Aosom\Marketing\Block\Adminhtml\Trial;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'trial_id';
        $this->_controller = 'adminhtml_trial';
        $this->_blockGroup = 'Aosom_Marketing';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }

    public function getHeaderText()
    {
        $trial = $this->_coreRegistry->registry('current_trial');
        if ($trial->getId()) {
            return __("Edit trial '%1'", $this->escapeHtml($trial->getName()));
        } else {
            return __('Add New trial');
        }
    }
}
