<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aosom\Marketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
/**
 * Gift Message Observer Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ResetTrialStockObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!trial_is_enabled()) return $this;
        $logFileName = 'Observer/run_'.m2_date('Y-m-d').'.log';
        $loger = m2_monolog('Observer',$logFileName);
        try {
            $objectManager = ObjectManager::getInstance();

            $order = $observer->getEvent()->getOrder();
            if ($order instanceof \Magento\Sales\Model\Order == true) {
                $items = $order->getAllItems();
                /** @var \Magento\Sales\Model\Order\Item $item */
                foreach ($items as $item) {
                    if ($item->getIsTrial()) {
                        $productId = $item->getProductId();
                        $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
                        $aosom_trial_quantity = 0;
                        if ($product->getCustomAttribute("aosom_trial_quantity")) {
                            $aosom_trial_quantity = $product->getCustomAttribute("aosom_trial_quantity")->getValue();
                        }
                        if ($aosom_trial_quantity > 0) {
                            $product->addAttributeUpdate(
                                "aosom_trial_quantity", $aosom_trial_quantity + 1, 0
                            );
                            $cacheManager = $objectManager->get(\Magento\Framework\App\CacheInterface::class);
                            $cacheManager->clean(['catalog_product_' . $productId]);
                        }
                    }
                }
            }
        }catch (\Exception $e){
            $loger->addNotice('ResetTrialStockObserver run failure:'.$e->getMessage());
        }
        return $this;
    }
}
