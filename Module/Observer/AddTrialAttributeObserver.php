<?php
namespace Aosom\Marketing\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;

/**
 * Trial Observer Model
 *
 */
class AddTrialAttributeObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!trial_is_enabled()) return $this;
        $logFileName = 'Observer/run_'.m2_date('Y-m-d').'.log';
        $loger = m2_monolog('Observer',$logFileName);
        try {
            $objectManager = ObjectManager::getInstance();
            $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
            $is_trial = $request->getParam('is_trial');
            if ($is_trial) {
                $quoteItem = $observer->getQuoteItem();
                $product = $observer->getProduct();
                $aosom_trial_status = 0;
                if ($product->getCustomAttribute("aosom_trial_status")) {
                    $aosom_trial_status = $product->getCustomAttribute("aosom_trial_status")->getValue();
                }
                if ($aosom_trial_status) {
                    $aosom_trial_quantity = 0;
                    if ($product->getCustomAttribute("aosom_trial_quantity")) {
                        $aosom_trial_quantity = $product->getCustomAttribute("aosom_trial_quantity")->getValue();
                    }
                    $aosom_trial_sale = 0;
                    if ($product->getCustomAttribute("aosom_trial_sale")) {
                        $aosom_trial_sale = $product->getCustomAttribute("aosom_trial_sale")->getValue();
                    }
                }
                if ($aosom_trial_status) {
//                $quoteId = $quoteItem->getQuoteId();
//                $productId = $quoteItem->getProductId();
//                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
//                $connection = $resource->getConnection();
//                $sql = "UPDATE quote_item set is_trial=$aosom_trial_status,trial_sale=$aosom_trial_sale WHERE quote_id=$quoteId and product_id=$productId";
//                $result = $connection->query($sql);

                    $quoteItem->setIsTrial($aosom_trial_status);
                    $quoteItem->setTrialSale($aosom_trial_sale);
                }
            }
        }catch (\Exception $e){
            $loger->addNotice('AddTrialAttributeObserver run failure:'.$e->getMessage());
        }
        return $this;
    }
}
