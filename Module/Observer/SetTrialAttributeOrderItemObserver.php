<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aosom\Marketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use think\Exception;

/**
 * Gift Message Observer Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SetTrialAttributeOrderItemObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!trial_is_enabled()) return $this;
        $logFileName = 'Observer/run_'.m2_date('Y-m-d').'.log';
        $loger = m2_monolog('Observer',$logFileName);
        try {
            $objectManager = ObjectManager::getInstance();
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $items = $quote->getItems();
            foreach ($items as $item) {
                $is_trial = $item->getIsTrial();
                if ($is_trial) {
                    $sku = $item->getSku();
                    $resourceProduct = $objectManager->get(\Magento\Catalog\Model\ResourceModel\Product::class);
                    $productId = $resourceProduct->getIdBySku($sku);
                    $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
                    $aosom_trial_quantity = 0;
                    if ($product->getCustomAttribute("aosom_trial_quantity")) {
                        $aosom_trial_quantity = $product->getCustomAttribute("aosom_trial_quantity")->getValue();
                    }
                    if ($aosom_trial_quantity > 0) {
                        $qty = $aosom_trial_quantity - 1;
                        $product->addAttributeUpdate(
                            "aosom_trial_quantity", $qty, 0
                        );
                        $order->setHaveTrial(1);
                        $cacheManager = $objectManager->get(\Magento\Framework\App\CacheInterface::class);
                        $cacheManager->clean(['catalog_product_' . $productId]);
                    }
                    $trial = $objectManager->create("\Aosom\Marketing\Model\TrialRepository");
                    $trial->buildReportId($order->getIncrementId(),$productId, $order->getCustomerId());
                }
            }
        }catch (\Exception $e){
            $loger->addNotice('SetTrialAttributeOrderItemObserver run failure:'.$e->getMessage());
        }
        return $this;
    }
}
