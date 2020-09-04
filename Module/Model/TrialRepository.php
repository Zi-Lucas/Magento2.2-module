<?php
namespace Aosom\Marketing\Model;

use Aosom\Cms\Model\Config\Source\ClientType;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Aosom\Marketing\Api\Data\TrialInterface;
use Aosom\Marketing\Api\TrialRepositoryInterface;
use Aosom\Marketing\Model\ResourceModel\Trial\CollectionFactory;

class TrialRepository implements TrialRepositoryInterface
{

    protected $trialFactory;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    protected $objectManager;

    protected $helper;
    /**
     * @param TrialFactory $trialFactory
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        TrialFactory $trialFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        $this->trialFactory = $trialFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $this->objectManager->get('\Aosom\Marketing\Helper\Data');
    }

    /**
     * @inheritdoc
     */
    public function save(TrialInterface $object)
    {
        try {
            $object->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException($e->getMessage());
        }
        return $object;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $object = $this->trialFactory->create()->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function delete(TrialInterface $object)
    {
        try {
            $object->delete();
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        try {
            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);
            $collection = $this->collectionFactory->create();
            foreach ($criteria->getFilterGroups() as $filterGroup) {
                $fields = [];
                $conditions = [];
                foreach ($filterGroup->getFilters() as $filter) {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
                if ($fields) {
                    $collection->addFieldToFilter($fields, $conditions);
                }
            }
            $searchResults->setTotalCount($collection->getSize());
            $sortOrders = $criteria->getSortOrders();
            if ($sortOrders) {
                /** @var SortOrder $sortOrder */
                foreach ($sortOrders as $sortOrder) {
                    $collection->addOrder(
                        $sortOrder->getField(),
                        ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                    );
                }
            }
            $collection->setCurPage($criteria->getCurrentPage());
            $collection->setPageSize($criteria->getPageSize());
            $objects = [];
            foreach ($collection as $objectModel) {
                $objects[] = $objectModel;
            }
            $searchResults->setItems($objects);
        } catch (\Exception $e) {
            return $searchResults;
        }
        return $searchResults;
    }

    /**
     * @param int $currentPage
     * @param int $pageSize
     * @return \Aosom\Catalog\Api\Data\SearchResultInterface
     */
    public function getTrialProduct($currentPage = 1, $pageSize = 30)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $request->setParam("showCoupon", 1);

        $filterBuilder = $objectManager->create('Magento\Framework\Api\FilterBuilder');
        $filter = $filterBuilder->setField('aosom_trial_status')
            ->setValue(1)
            ->setConditionType('eq')
            ->create();
        $searchCriteriaBuilder = $objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter($filter);
        $searchCriteriaBuilder->addSortOrder('bestseller',SortOrder::SORT_DESC);
        $searchCriteriaBuilder->setPageSize($pageSize);
        $searchCriteriaBuilder->setCurrentPage($currentPage);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setRequestName('catalog_product_autocomplete');

        $productRepository = $objectManager->get('Aosom\Catalog\Model\ProductRepository');
        return $productRepository->getSearchList($searchCriteria);
    }

    /**
     * @param $request
     * @param $param
     * @param $type
     */
    private function parseParams($request,$param,$type){

    }

    /**
     * @param string $clientType
     * @param int $currentPage
     * @return array[]
     */
    public function homeMerge($clientType='PC',$currentPage=1)
    {
        $return = [];
        $trialRule = $this->helper->getTrialRule();
        $rule_des['title'] = $trialRule['title'];
        if ($clientType == ClientType::HTML5) {
            $img = $trialRule['simg'];
        } else {
            $img = $trialRule['bimg'];
        }
        $rule_des['img'] = $img;
        $rule_des['summary'] = $trialRule['summary'];
        $rule_des['data_json'] = $trialRule['data_json'];
        $return['rule_des'] = $rule_des;
        if ($currentPage == 1) {
            $trialProtocol = $this->helper->getTrialProtocol();
            $protocol_des['title'] = $trialProtocol['title'];
            if ($clientType == ClientType::HTML5) {
                $img = $trialProtocol['simg'];
            } else {
                $img = $trialProtocol['bimg'];
            }
            $protocol_des['img'] = $img;
            $protocol_des['summary'] = $trialProtocol['summary'];
            $protocol_des['data_json'] = $trialProtocol['data_json'];
            $return['protocol_des'] = $protocol_des;
        }

        $products = $this->getTrialProduct($currentPage);
        $return['products'] = $this->productFiledFilter($products);

        return [$return];
    }

    private function productFiledFilter($searchResult)
    {
        $products = $searchResult->getItems();
        $productsArr = [];
        foreach ($products as $product){
            $extension = $product->getExtensionAttributes();

            $sku = $product->getSku();
            $entity_id = $product->getIdBySku($sku);
            $name = $product->getName();
            $price = $product->getPrice();
            $quantityAndStockStatus = $product->getQuantityAndStockStatus();
            $quantity = $quantityAndStockStatus['qty'];
//            $quantity = $product->getQty();
            $special_price = $product->getSpecialPrice();
            $url_key = $product->getUrlKey();
            $is_bestseller = $product->getIsBestseller();
            $is_flash_sale = $product->getIsFlashSale();
            $aosom_custom_tag = $product->getAosomCustomTag();
            $final_price = $extension->getFinalPrice();
            $medium_image_url = $extension->getMediumImageUrl();
            $is_new = $extension->getIsNew();
            $productArr = array(
                "sku" => $sku,
                "entity_id" => $entity_id,
                "name" => $name,
                "price" => $price,
                "quantity" => $quantity,
                "special_price" => $special_price,
                "url_key" => $url_key,
                "is_bestseller" => $is_bestseller,
                "is_flash_sale" => $is_flash_sale,
                "aosom_custom_tag" => $aosom_custom_tag,
                "final_price" => $final_price,
                "medium_image_url" => $medium_image_url,
                "is_new" => $is_new,
            );

            $aosom_trial_status = 0;
            if ($product->getCustomAttribute("aosom_trial_status")) {
                $aosom_trial_status = $product->getCustomAttribute("aosom_trial_status")->getValue();
            }
            $aosom_trial_sale = 100;
            if ($product->getCustomAttribute("aosom_trial_sale")) {
                $aosom_trial_sale = $product->getCustomAttribute("aosom_trial_sale")->getValue();
            }
            $aosom_trial_quantity = 0;
            if ($product->getCustomAttribute("aosom_trial_quantity")) {
                $aosom_trial_quantity = $product->getCustomAttribute("aosom_trial_quantity")->getValue();
                if ($aosom_trial_quantity>$quantity) $aosom_trial_quantity = $quantity;
            }
            if ($aosom_trial_status) {
                $productArr['aosom_trial_status'] = intval($aosom_trial_status);
                $productArr['aosom_trial_sale'] = intval($aosom_trial_sale);
                $productArr['aosom_trial_quantity'] = intval($aosom_trial_quantity);
            }
            $productsArr[] = $productArr;
        }
        return $productsArr;
    }

    /**
     * @param string $clientType
     * @param int $currentPage
     * @return array[]
     */
    public function reportsHomeMerge($clientType='PC',$currentPage=1)
    {

    }

    /**
     * @param string $reportId
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getReport($reportId)
    {
        $report = $this->getById($reportId);
        if (!$report || !$report->getId()){
            api_return_message(400,'On Error',array('err_msg'=>'Trial Report is missing'));
        }
        $report = $report->getData();
        if (isset($report['trial_id'])) unset($report['trial_id']);
        if ($report['imgs']){
            $report['imgs'] = unserialize($report['imgs']);
        }
//        $orderId = $report['increment_id']??0;
//        if (empty($orderId)) {
//            return [];
//        }
//        $order = $this->objectManager->create(\Magento\Sales\Model\Order::class);
//        $order->loadByIncrementId($orderId);
//        $orderIterms = $order->getItems();
//        $products = [];
//        foreach ($orderIterms as $item) {
//            $is_trial = $item->getIsTrial();
//            if ($is_trial){
//                $productId = $item->getProductId();
//                $product = $this->objectManager->create(Product::class)->load($productId);
//                $products[] = $this->productFieldFilter($product);
//            }
//        }
        $productId = $report['product_id']??0;
        if (empty($productId)) {
            return [];
        }
        $product = $this->objectManager->create(Product::class)->load($productId);
        $product = $this->productFieldFilter($product);
        return [array(
            "report" => $report,
            "product" => $product,
        )];
    }

    /**
     * @param int $product_id
     * @param int $currentPage
     * @param int $pageSize
     * @return array
     */
    public function getProductReport($product_id,$currentPage = 1, $pageSize = 3)
    {
        $filterBuilder = $this->objectManager->create('Magento\Framework\Api\FilterBuilder');
        $filter = $filterBuilder->setField('status')
            ->setValue(5)
            ->setConditionType('eq')
            ->create();
        $filter2 = $filterBuilder->setField('product_id')
            ->setValue($product_id)
            ->setConditionType('eq')
            ->create();
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter($filter);
        $searchCriteriaBuilder->addFilter($filter2);
        $searchCriteriaBuilder->addSortOrder('updated_at',SortOrder::SORT_DESC);
        $searchCriteriaBuilder->setPageSize($pageSize);
        $searchCriteriaBuilder->setCurrentPage($currentPage);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria = $this->getList($searchCriteria);
        $list = m2_object_2_array($searchCriteria,"\Magento\Framework\Api\SearchResultsInterface");
        $items = $searchCriteria->getItems();
        foreach ($items as $key => $item){
            $list['items'][$key] = $item->getData();
            if ($list['items'][$key]['content'] && strlen($list['items'][$key]['content'])>1002){
                $list['items'][$key]['content'] = substr($list['items'][$key]['content'], 0,1002)."...";
            }
            $list['items'][$key]['content'] = mb_convert_encoding($list['items'][$key]['content'], 'UTF-8','HTML-ENTITIES');
            $list['items'][$key]['title'] = mb_convert_encoding($list['items'][$key]['title'], 'UTF-8','HTML-ENTITIES');
            $list['items'][$key]['customer_name'] = mb_convert_encoding($list['items'][$key]['customer_name'], 'UTF-8','HTML-ENTITIES');
            if ($list['items'][$key]['imgs']){
                $list['items'][$key]['imgs'] = unserialize($list['items'][$key]['imgs']);
            }
        }
        return [$list];
    }
    /**
     * 过滤产品字段
     * @param Product $product
     * @return array
     */
    private function productFieldFilter($product){
        $productArr = $product->getData();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->get('Aosom\Catalog\Model\ProductRepository');
        $image = $productArr["image"]??"";
        if ($image)  $image = $productRepository->getS3ImgUrl($image,'200x200');
        return array(
            "sku" => $productArr["sku"]??"",
            "entity_id" => $productArr["entity_id"]??"",
            "name" => $productArr["name"]??"",
            "price" => $productArr["price"]??"",
            "special_price" => $productArr["special_price"]??"",
            "final_price" => $product->getFinalPrice()??"",
            "url_key" => $productArr["url_key"]??"",
            'image' => $image,
//            "is_saleable" => $is_saleable,
//            'is_bestseller' => $productArr["is_bestseller"]??0,
//            'is_flash_sale' => $productArr["is_flash_sale"]??0,
//            'aosom_custom_tag' => $productArr["aosom_custom_tag"]??"",
//            'is_new' => $productArr['extension_attributes']->getIsNew()??0,
//            'discount_percent' => $productArr['extension_attributes']->getDiscountPercent()??""
        );
    }
    /**
     * @param int $currentPage
     * @return array[]
     */
    public function getReports($currentPage=1)
    {

    }

    /**
     * @param $order_id
     * @param $productId
     * @param $customer_id
     * @return \Aosom\Marketing\Api\Data\TrialInterface
     * @throws LocalizedException
     */
    public function buildReportId($order_id,$productId,$customer_id=0)
    {
        if (trial_is_enabled()) {
            $reportId = md5($order_id . $customer_id . time() . rand());
            $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $sql = "INSERT INTO `aosom_trial_report` (`trial_id`,`customer_id`, `product_id`, `increment_id`, `status`)
                    VALUES ('$reportId', $customer_id, $productId, $order_id,1)";
            try {
                $logFileName = 'Trail/BuildReportId_' . m2_date('Y-m') . '.log';
                $loger = m2_monolog('TrailReport', $logFileName);
                if (!$connection->query($sql)) {
                    $loger->addNotice('customer[' . $customer_id . '] order[' . $order_id . '] Build trial report failure');
                }
            } catch (\Exception $e) {
                $loger->addNotice('customer[' . $customer_id . '] order[' . $order_id . '] Build trial report failure');
            }
            $trial = $this->objectManager->create("\Aosom\Marketing\Api\Data\TrialInterface");
            $trial->load($reportId);
            return $trial;
        }else{
            return true;
        }
    }

    /**
     * @param TrialInterface $trial
     * @return \Aosom\Marketing\Api\Data\TrialInterface
     * @throws LocalizedException
     */
    public function addReport(TrialInterface $trial)
    {
        if (trial_is_enabled()) {
            $trial = $this->validatedReportParams($trial);
            $order = $this->objectManager->create(\Magento\Sales\Model\Order::class);
            $orderInfo = $order->loadByIncrementId($trial->getOrderId());
            if (!$orderInfo || !$orderInfo->getId()) {
                api_return_message(400, 'On Error', array('err_msg' => 'Order is missing'));
            }

            $customer = $this->objectManager->create('\Magento\Customer\Model\Customer');
            $customerInfo = $customer->load($trial->getCustomerId());
            if (!$customerInfo || !$customerInfo->getId()) {
                api_return_message(400, 'On Error', array('err_msg' => 'Customer[' . $trial->getCustomerId() . '] is missing'));
            }
            $trial->save();
            $trial->setImgs(unserialize($trial->getImgs()));
            return $trial;
        }else{
            api_return_message(400, 'On Error', array('err_msg' => 'trial disabled'));
        }
    }

    /**
     * @param string $reportId
     * @param TrialInterface $trial
     * @return \Aosom\Marketing\Api\Data\TrialInterface
     * @throws LocalizedException
     */
    public function modifyReport($reportId,TrialInterface $trial)
    {
        if (trial_is_enabled()) {
            $report = $this->getById($reportId);
            if (!$report || !$report->getId()){
                api_return_message(400,'On Error',array('err_msg'=>'Trial Report is missing'));
            }
            if ( in_array($report->getStatus(),[5])){
                api_return_message(400,'On Error',array('err_msg'=>'The current state cannot be modified'));
            }
            $trial = $this->validatedReportParams($trial,true);

            $trial->setTrialId($reportId);
            $trial->save();
            $trial->setImgs(unserialize($trial->getImgs()));
            return $trial;
        }else{
            api_return_message(400, 'On Error', array('err_msg' => 'trial disabled'));
        }
    }

    /**
     * @param TrialInterface $trial
     * @param boolean $modify
     * @return TrialInterface
     * @throws LocalizedException
     */
    private function validatedReportParams($trial,$modify = false)
    {
        if ($modify === false) {
            $order_id = intval($trial->getOrderId());
            if (empty($order_id)) {
                api_return_message(400, 'On Error', array('err_msg'=> 'Order Id is missing'));
            }
            $customer_id = intval($trial->getCustomerId());
            if (empty($customer_id)) {
                api_return_message(400, 'On Error', array('err_msg'=> 'Customer Id is missing'));
            }
        }
        $customer_name = trim($trial->getCustomerName()," ");
        if (empty($customer_name)){
            api_return_message(400,'On Error',array('err_msg'=>'Customer Name is missing'));
        }
        $title = trim($trial->getTitle()," ");
        if (empty($title)){
            api_return_message(400,'On Error',array('err_msg'=>'Title is missing'));
        }
        $content = trim($trial->getContent()," ");
        if (empty($content)){
            api_return_message(400,'On Error',array('err_msg'=>'Content is missing'));
        }
        $imgs = $trial->getImgs();
        $imgs_arr = array_values(array_unique(explode('|',trim($imgs," "))));
        if (empty($imgs_arr)){
            api_return_message(400,'On Error',array('err_msg'=>'Imgs is missing'));
        }
        $video = $trial->getVideo();
        if ($video) {
            if (!preg_match('/http[s]{0,1}:\/\//', $video)) {
                api_return_message(400, 'On Error', array('err_msg' => 'Video Link is missing'));
            }
        }
        if (strlen($customer_name) > 50) {
            api_return_message(400,'On Error',array('err_msg'=>'Customer Name must less than 50 char'));
        }
        if (strlen($title) > 200) {
            api_return_message(400,'On Error',array('err_msg'=>'Title must less than 200 char'));
        }
        if (strlen($content) > 8000) {
            api_return_message(400,'On Error',array('err_msg'=>'Content must less than 8000 char'));
        }
        if (count($imgs_arr) > 9){
            api_return_message(400,'On Error',array('err_msg'=>'Images must less than 9'));
        }

        $escaper = $this->objectManager->create('\Magento\Framework\Escaper');
        $title = $escaper->escapeHtml($title,['p','div','span']);
        $content = $escaper->escapeHtml($content,['p','div','span']);
        $customer_name = $escaper->escapeHtml($customer_name,['p','div','span']);
        $video = trim($video,' ');
        $imgs = [];
        foreach ($imgs_arr as $img){
            if (preg_match('/http[s]{0,1}:\/\//', $img)) {
                $imgs[] = $img;
            }
        }
        if ($modify === false) {
            $trial->setCustomerId($customer_id);
            $trial->setOrderId($order_id);
        }
        $trial->setContent($content);
        $trial->setCustomerName($customer_name);
        $trial->setImgs(serialize(array_values($imgs_arr)));
        $trial->setTitle($title);
        $trial->setVideo($video);
        $trial->setStatus(3);

        return $trial;
    }
}
