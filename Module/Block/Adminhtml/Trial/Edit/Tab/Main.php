<?php

namespace Aosom\Marketing\Block\Adminhtml\Trial\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class Main extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
    protected $_status;
    protected $_pieceType;
    protected $_imageType;
    protected $_yesno;
    protected $_systemStore;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('General');
    }

    public function getTabTitle()
    {
        return __('General');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_trial');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('trial_general_');
        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('trial_id', 'hidden', ['name' => 'trial_id']);
            $fieldset->addField('created_at', 'hidden', ['name' => 'created_at']);
        }
        $fieldset->addField(
            'customer_name',
            'text',
            ['name' => 'customer_name', 'label' => __('Name'), 'title' => __('Name'), 'required' => false]
        );
        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Title'), 'title' => __('Title'), 'required' => false]
        );
        $fieldset->addField(
            'content','editor', [
                'name' => 'content',
                'label' => __('Data'),
                'title' => __('Data'),
                'required' => false,
                'style' => 'height:800px',
                'config' => $this->_wysiwygConfig->getConfig(['add_directives' => true,'height'=>'800px', 'hidden'=> false])
            ]
        );
        $fieldset->addField(
            'video',
            'text',
            ['name' => 'video', 'label' => __('Video'), 'video' => __('Video'), 'required' => false]
        );
        $imgs = $model->getImgs();
        if (!empty($imgs)){
            $imgs = unserialize($imgs);
        }
        $values = $model->getData();
//        $key = 0;
//        foreach ($imgs as $key => &$img){
//            $img_arr = parse_url($img);
//            $img = trim(strstr(trim($img_arr['path'],'/'),'/'),'/');
//            $values['img'.$key] = $imgs[$key];
//            $fieldset->addField(
//                'img'.$key,
//                'image',
//                ['name' => "imgs[$key]", '','label' => __('Image'), 'title' => __('Image'), 'required' => false]
//            );
//        }
//        if ($key) $key += 1;
//        for (;$key<9;$key++){
//            $values['img'.$key] = "";
//            $fieldset->addField(
//                'img'.$key,
//                'image',
//                ['name' => "imgs[$key]", '','label' => __('Image'), 'title' => __('Image'), 'required' => false]
//            );
//        }
        $form->setValues($values);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
