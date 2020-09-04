<?php
namespace Aosom\Marketing\Controller\Adminhtml\Testreport;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aosom\Marketing\Model\Export\ConvertToPdf;
use Magento\Framework\App\Response\Http\FileFactory;

class DownlodeToPdf extends Action
{
    /**
     * @var ConvertToPdf
     */
    private $converter;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Context $context,
        ConvertToPdf $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);

        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->converter->getPdfFile();die;
        return $this->fileFactory->create('export.csv', $this->converter->getCsvFile(), 'var');
    }
}
