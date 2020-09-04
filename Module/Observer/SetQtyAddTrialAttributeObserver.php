<?php
namespace Aosom\Marketing\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Trial Observer Model
 *
 */
class SetQtyAddTrialAttributeObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!trial_is_enabled()) return $this;
        try {
            $objectManager = ObjectManager::getInstance();
            $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
            $is_trial = $request->getParam('is_trial');
            if ($is_trial) {
                $quoteItem = $observer->getItem();
                if (!$this->check($quoteItem)) return $this;
                $quoteId = $quoteItem->getQuoteId();
                $productId = $quoteItem->getProductId();
                $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
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
                if ($aosom_trial_status && $aosom_trial_quantity) {
                    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                    $connection = $resource->getConnection();
                    $sql = "UPDATE quote_item set is_trial=$aosom_trial_status,trial_sale=$aosom_trial_sale WHERE quote_id=$quoteId and product_id=$productId";
                    $result = $connection->query($sql);
                }
            }
        }catch (\Exception $e){
            return $this;
        }
        return $this;
    }

    /**
     * @param $quoteItem
     * @return bool
     */
    private function check($quoteItem)
    {
        $quoteId = $quoteItem->getQuoteId();
        if (empty($quoteId)) return false;
        $objectManager = ObjectManager::getInstance();
        $customerId = get_customer_id();
        if (empty($customerId)) return false;

            if($quoteItem->getQty() < 1){
                return false;
            }
            $sku = $quoteItem->getSku();

            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();

            $sql = "SELECT COUNT(*) FROM quote_item where quote_id=$quoteId and is_trial=1";
            $result = $connection->fetchAll($sql);
            if (count($result)>0){
                return false;
            }

            $sql = "SELECT status,soi.is_trial,soi.trial_sale from sales_order as so
                        LEFT JOIN sales_order_item as soi on so.entity_id=soi.order_id
                        WHERE so.status != 'canceled' and so.customer_id=$customerId and soi.sku='$sku' and soi.is_trial=1";
            $result = $connection->fetchAll($sql);
            if (count($result)>0){
                return false;
            }
            $resourceProduct = $objectManager->get(\Magento\Catalog\Model\ResourceModel\Product::class);
            $productId = $resourceProduct->getIdBySku($sku);
            $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
            $aosom_trial_status = 0;
            if ($product->getCustomAttribute("aosom_trial_status")) {
                $aosom_trial_status = $product->getCustomAttribute("aosom_trial_status")->getValue();
            }
            if ($aosom_trial_status) {
                $aosom_trial_quantity = 0;
                if ($product->getCustomAttribute("aosom_trial_quantity")) {
                    $aosom_trial_quantity = $product->getCustomAttribute("aosom_trial_quantity")->getValue();
                }
            }else{
                return false;
            }
            $quantityAndStockStatus = $product->getQuantityAndStockStatus();
            $quantity = $quantityAndStockStatus['qty'];
            if ($quantity && $aosom_trial_quantity) {
                return true;
            }else{
                return false;
            }
    }
}
