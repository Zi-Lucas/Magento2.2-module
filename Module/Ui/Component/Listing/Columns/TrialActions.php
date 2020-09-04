<?php

namespace Aosom\Marketing\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class TrialActions extends Column
{
    const URL_PATH_EDIT = 'aosom_marketing/trial/edit';
    const URL_PATH_PREVIEW = 'aosom_marketing/trial/preview';
    const URL_PATH_INVITE = 'aosom_marketing/trial/invite';
    const URL_PATH_PASS = 'aosom_marketing/trial/pass';
    const URL_PATH_NOTPASS = 'aosom_marketing/trial/nopass';
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['trial_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['trial_id' => $item['trial_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['preview'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_PREVIEW, ['trial_id' => $item['trial_id']]),
                        'label' => __('Preview'),
                        'target' => "_blank"
                    ];
                    $item[$name]['invite'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_INVITE, ['trial_id' => $item['trial_id']]),
                        'label' => __('Send Email (Invite)'),
                        'confirm' => [
                            'title' => __('Send the trial report invitation email!'),
                            'message' => __('Are you sure you wan\'t to send the trial report invitation email to ${ $.$data.customer_id }?')
                        ]
                    ];
                    $item[$name]['pass'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_PASS, ['trial_id' => $item['trial_id']]),
                        'label' => __('Send Email (Pass)'),
                        'confirm' => [
                            'title' => __('Send passed the trial report email!'),
                            'message' => __('Send passed the trial report email to ${ $.$data.customer_id }?')
                        ]
                    ];
                    $item[$name]['nopass'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_NOTPASS, ['trial_id' => $item['trial_id']]),
                        'label' => __('Send Email (NoPass)'),
                        'confirm' => [
                            'title' => __('Send no passed the trial report email!'),
                            'message' => __('Send no passed the trial report email to ${ $.$data.customer_id }?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
