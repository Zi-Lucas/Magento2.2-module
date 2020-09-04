<?php

namespace Aosom\Marketing\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class TestreportActions extends Column
{
    const URL_PATH_PREVIEW = 'aosom_marketing/testreport/preview';
    const URL_PATH_DOWNLOAD_PDF = 'aosom_marketing/testreport/downlodeToPdf';

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
                if (isset($item['id'])) {
                    $item[$name]['preview'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_PREVIEW, ['id' => $item['id']]),
                        'label' => __('Preview'),
                        'target' => "_blank"
                    ];
                    $item[$name]['download-pdf'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_DOWNLOAD_PDF, ['id' => $item['id']]),
                        'label' => __('Download PDF'),
                        'confirm' => [
                            'title' => __('Download PDF!'),
                            'message' => __('Are you sure you wan\'t to download PDF-report?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
