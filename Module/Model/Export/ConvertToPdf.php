<?php
namespace Aosom\Marketing\Model\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\ObjectManagerInterface;
use Aosom\Marketing\Helper\Data;

class ConvertToPdf
{
    /**
     * @var Data
     */
    private $data;
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;
    /**
     * @var MetadataProvider
     */
    private $metadataProvider;
    /**
     * @var int
     */
    private $pageSize;

    public function __construct(
        Data $data,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        $pageSize = 200
    ) {
        $this->data = $data;
        $this->filter = $filter;
        try {
            $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        } catch (FileSystemException $e) {
        }
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
    }
    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $namespace = $request->getParam('namespace');
        $name = md5(microtime());
        $file = 'export/'. $namespace . $name . '.csv';

        $id = $request->getParam('id');
        $data = $this->data->getTestReportData($id);
        $trim = $data->getData();
        if ($trim){
            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $pcBrowser = [5=>'others',4=>'Firefox',3=>'Edge',2=>'Chrome',1=>'Safari'];
            $MobileBrand = [5=>'others',4=>'Google',3=>'Huawei',2=>'Sumsung',1=>'Apple'];
            $TabletBrand = [5=>'others',4=>'Sumsung Galaxy Tab',3=>'Windows RT Tablet',2=>'Amazon Fire',1=>'Apple Ipad'];
            $evaluation = [5=>'Very satisfied',4=>'Satisfied',3=>'So-so',2=>'Disappointed',1=>'Very disappointed'];
            foreach ($trim as $k => $v){
                $d = $v;
                if(in_array($k,['loading_speed','ui','searching','category','promotions','text_input','payment_method','payment_process'])){
                    $d = $evaluation[$v];
                }
                if (in_array($k,['pc_browser']) && $v){
                    $d = "";
                    foreach (explode(',',$v) as $i){
                        $d .= $pcBrowser[$i] . ",";
                    }
                }
                if (in_array($k,['mobile_brand']) && $v){
                    $d = "";
                    foreach (explode(',',$v) as $i){
                        $d .= $MobileBrand[$i] . ",";
                    }
                }
                if (in_array($k,['tablet_brand']) && $v){
                    $d = "";
                    foreach (explode(',',$v) as $i){
                        $d .= $TabletBrand[$i] . ",";
                    }
                }
                $stream->writeCsv([$k,$d]);
            }
            $stream->unlock();
            $stream->close();
        }
        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true  // can delete file after use
        ];
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getPdfFile()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $namespace = $request->getParam('namespace');
        $name = md5(microtime());
        $file = 'export/'. $namespace . $name . '.pdf';
        $id = $request->getParam('id');
        $data = $this->data->getTestReportData($id);
        $data = $data->getData();
        $pc_browser_str = "";
        if ($data['pc_browser']){
            $pcBrowser = [5=>'others',4=>'Firefox',3=>'Edge',2=>'Chrome',1=>'Safari'];
            $pc_browser = explode(',',$data['pc_browser']);
            foreach ($pc_browser as $pb){
                $pc_browser_str .= $pcBrowser[$pb];
            }
        }

        $mobile_brand_str = "";
        if ($data['pc_browser']){
            $MobileBrand = [5=>'others',4=>'Google',3=>'Huawei',2=>'Sumsung',1=>'Apple'];
            $mobile_brand = explode(',',$data['mobile_brand']);
            foreach ($mobile_brand as $mb){
                $mobile_brand_str .= $MobileBrand[$mb];
            }
        }

        $tablet_brand_str = "";
        if ($data['pc_browser']){
            $TabletBrand = [5=>'others',4=>'Sumsung Galaxy Tab',3=>'Windows RT Tablet',2=>'Amazon Fire',1=>'Apple Ipad'];
            $tablet_brand = explode(',',$data['tablet_brand']);
            foreach ($tablet_brand as $tb){
                $tablet_brand_str .= $TabletBrand[$tb];
            }
        }

        $evaluation = [5=>'Very satisfied',4=>'Satisfied',3=>'So-so',2=>'Disappointed',1=>'Very disappointed'];

        require_once(BP.'/vendor/tecnickcom/tcpdf/tcpdf.php');
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Customer experience test report');
        $pdf->SetSubject('AOSOM Test Report');
        $pdf->SetKeywords('AOSOM, test report');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Customer experience test report', "https://".$_SERVER['HTTP_HOST'], array(0,0,0), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->AddPage();
        $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $html = <<<EOD
<style>
    span {
    display: inline-block;padding: 4px;
    }
</style>
<div class="container">
    <div class="group" style="padding: 10px 2px;">
        <div class="group-title" style="padding: 2px;font-weight: bold;font-size: 18px;">Part 1  Testing Informations</div>
        <div class="text-muted" style="padding: 2px;color: #929292;font-size: 16px;">Please write down the test email & order so that we can follow up.</div>
        <div class="group-body" style="padding: 10px;color: #505050;font-size: 14px;">
            <div><span style="font-weight: bold;width: 10%;font-weight: bold;display: inline-block;padding: 4px;">Email:  </span><span>1234556@QQ.COM</span></div>
            <div><span style="font-weight: bold;width: 10%;font-weight: bold;display: inline-block;padding: 4px;">Order ID:  </span><span>1234556</span></div>
        </div>
    </div>

    <div class="group" style="padding: 10px 2px;">
        <div class="group-title" style="padding: 2px;font-weight: bold;font-size: 18px;">Part 2  Browser/mobile type</div>
        <div class="text-muted" style="padding: 2px;color: #929292;font-size: 16px;">Please select the type of equipment you are testing （Multiple choice）.</div>
        <div class="group-body" style="padding: 10px;color: #505050;font-size: 14px;">
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">PC Browser:  </span><span>$pc_browser_str</span><span style="color: #929292;">{$data['pc_browser_explain']}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Mobile Brand:  </span><span>$mobile_brand_str</span><span style="color: #929292;">{$data['mobile_brand_explain']}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Tablet Brand:  </span><span>$tablet_brand_str</span><span style="color: #929292;">{$data['tablet_brand_explain']}</span></div>
        </div>
    </div>

    <div class="group" style="padding: 10px 2px;">
        <div class="group-title" style="padding: 2px;font-weight: bold;font-size: 18px;">Part 3  Problems of site</div>
        <div class="text-muted" style="padding: 2px;color: #929292;font-size: 16px;">Please submit the found problems and screenshots.</div>
        <div class="group-body" style="padding: 10px;color: #505050;font-size: 14px;">
            <div><span style="font-weight: bold;width: 10%";>Problem page:  </span></div>
            <div style="background-color: #eaeaea;">
                {$data['problem_page_description']}
            </div>
        </div>
    </div>

    <div class="group" style="padding: 10px 2px;">
        <div class="group-title" style="padding: 2px;font-weight: bold;font-size: 18px;">Part 4 Shopping experience</div>
        <div class="group-body" style="padding: 10px;color: #505050;font-size: 14px;">
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Loading Speed:  </span><span>{$evaluation[$data['loading_speed']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">UI:  </span><span>{$evaluation[$data['ui']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Searching:  </span><span>{$evaluation[$data['searching']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Category:  </span><span>{$evaluation[$data['category']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Promotions:  </span><span>{$evaluation[$data['promotions']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Text Input:  </span><span>{$evaluation[$data['text_input']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Payment Method:  </span><span>{$evaluation[$data['payment_method']]}</span></div>
            <div><span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Payment Process:  </span><span>{$evaluation[$data['payment_process']]}</span></div>
            <div>
                <span style="font-weight: bold;width: 10%;display: inline-block;padding: 4px;">Suggestions:  </span>
                <div style="background-color: #eaeaea;">
                    {$data['suggestions']}
                </div>
            </div>

        </div>
    </div>
</div>
EOD;

        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $pdf->Output('report-'.time().'.pdf', 'I');
    }
}
