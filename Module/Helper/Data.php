<?php
namespace Aosom\Marketing\Helper;

use Magento\Framework\Config\File\ConfigFilePool;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;

    protected $_objectManager;

    protected $_apiCustomer;

    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $context->getScopeConfig();
    }

    public function backendFrontName()
    {
        $storeManager = $this->_objectManager->create(\Magento\Framework\App\DeploymentConfig::class);
        return $storeManager->get('backend/frontName');
    }

    /**
     * @param $code ['invite','pass','no_pass']
     * @return mixed
     */
    public function trialEmailTemplete($code)
    {
        $emailTemplate = $this->_scopeConfig->getValue(
            "aosom_marketing/trial/report_" . $code . "_email_template",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (empty($emailTemplate)) {
                switch ($code) {
                    case 'pass':
                        $emailTemplate = 'aosom_marketing_trial_report_pass_email_template';
                        break;
                    case 'no_pass':
                        $emailTemplate = 'aosom_marketing_trial_report_no_pass_email_template';
                        break;
                    default :
                        $emailTemplate = 'aosom_marketing_trial_report_invite_email_template';
                        break;
            }
        }

        return $emailTemplate;
    }

    /**
     * @param string $from
     * @param $to
     * @param $toname
     * @param $template
     * @param array $param
     * @return bool
     */
    public function sendEmail($from='',$to,$toname="",$template,$param=[]){
        if (empty($template) || empty($to)) return false;
        $storeManager = $this->_objectManager->create(\Magento\Store\Model\StoreManagerInterface::class);
        $transportBuilder = $this->_objectManager->create(\Magento\Framework\Mail\Template\TransportBuilder::class);
        $contactConfig = $this->_objectManager->create(\Magento\Contact\Model\ConfigInterface::class);
        $inlineTranslation = $this->_objectManager->create(\Magento\Framework\Translate\Inline\StateInterface::class);
        $inlineTranslation->suspend();
        $from = $contactConfig->emailRecipient();
        try {
            $transport = $transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars($param)
                ->setFrom($contactConfig->emailSender())
                ->addTo($to)
//                ->addTo($from)
//                ->setReplyTo($to, $toname)
                ->getTransport();
            $transport->sendMessage();
        } finally {
            $inlineTranslation->resume();
        }
        return true;
    }

    public function getTrialRule()
    {
        $pieceId = $this->_scopeConfig->getValue(
            "aosom_marketing/trial/trial_rule_cms_id",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (empty($pieceId)) {
            return [];
        }
        $pieceManager = $this->_objectManager->create(\Aosom\Cms\Model\PieceRepository::class);
        $piece = $pieceManager->getById($pieceId)->getData();
        return $piece;
    }

    public function trialIsEnabled()
    {
        return $this->_scopeConfig->getValue(
            "aosom_marketing/trial/enabled",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getTrialProtocol()
    {
        $pieceId = $this->_scopeConfig->getValue(
            "aosom_marketing/trial/trial_protocol_cms_id",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (empty($pieceId)) {
            return [];
        }
        $pieceManager = $this->_objectManager->create(\Aosom\Cms\Model\PieceRepository::class);
        $piece = $pieceManager->getById($pieceId)->getData();
        return $piece;
    }

    /**
     * @param $id
     * @return bool
     */
    public function getTestReportData($id)
    {
        if (empty($id)) {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $trialRepository =$objectManager->get('Aosom\Marketing\Model\TestreportRepository');
        return $trialRepository->getById($id);
    }
}
