<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aosom\Marketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Gift Message Observer Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CheckTrialStockObserver implements ObserverInterface
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
            $items = $quote->getItems();
            $trial_skus = [];
            $resourceProduct = $objectManager->get(\Magento\Catalog\Model\ResourceModel\Product::class);
            foreach ($items as &$item){
                $is_trial = $item->getIsTrial();
                if ($is_trial){
                    $sku = $item->getSku();

                    $productId = $resourceProduct->getIdBySku($sku);
                    $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
                    $aosom_trial_quantity = 0;
                    if ($product->getCustomAttribute("aosom_trial_quantity")) {
                        $aosom_trial_quantity = $product->getCustomAttribute("aosom_trial_quantity")->getValue();
                    }
                    if ($aosom_trial_quantity <= 0){
    //                    throw new \Magento\Framework\Exception\LocalizedException(
    //                        __('Not all of your trial products are available in the requested quantity 1.')
    //                    );
                        $item->setIsTrial(0);
                        $item->setTrialSale(0);
                    }else{
                        $trial_skus[] = $sku;
                    }
                }
            }
            $quote->setItems($items);
            $order = $observer->getOrder();
            if (empty($trial_skus)){
                $order->setHaveTrial(0);
            }else {
                $items = $order->getItems();
                foreach ($items as &$item) {
                    $sku = $item->getSku();
                    if (in_array($sku, $trial_skus)) {
                        $productId = $resourceProduct->getIdBySku($sku);
                        $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
                        $aosom_trial_sale = "";
                        if ($product->getCustomAttribute("aosom_trial_sale")) {
                            $aosom_trial_sale = $product->getCustomAttribute("aosom_trial_sale")->getValue();
                        }
                        $item->setIsTrial(1);
                        $item->setTrialSale($aosom_trial_sale);
                    }
                }
                $order->setItems($items);
            }
        }catch (\Exception $e){
            $loger->addNotice('CheckTrialStockObserver run failure:'.$e->getMessage());
        }
        return $this;
    }
}
