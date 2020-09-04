<?php
namespace Aosom\Marketing\Api\Data;

interface TestreportInterface {

    /**
     * Constants for field names.
     */
    const ID = 'id';
    const EMAIL = 'email';
    const ORDER_ID = 'order_id';
    const PC_BROWSER = 'pc_browser';
    const PC_BROWSER_EXPLAIN = 'pc_browser_explain';
    const MOBILE_BRAND = 'mobile_brand';
    const MOBILE_BEAND_EXPLAIN = 'mobile_brand_explain';
    const TABLET_BEAND = 'tablet_brand';
    const TABLET_BEAND_EXPLAIN = 'tablet_brand_explain';
    const PROBLEM_PAGE_DESCRIPTION = 'problem_page_description';

    const LOADING_SPEED = 'loading_speed';
    const UI = 'ui';
    const SEARCHING = 'searching';
    const CATEGORY = 'category';
    const PROMORTIONS = 'promotions';
    const TEXT_INPUT = 'text_input';
    const PAYMENT_METHOD = 'payment_method';
    const PAYMENT_PROCESS = 'payment_process';
    const SUGGESTIONS = 'suggestions';

    const CREATION_AT = 'created_at';
    const STATUS = 'status';
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);
    /**
     * @return string|null
     */
    public function getEmail();
    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);
    /**
     * @return string|null
     */
    public function getOrderId();
    /**
     * @param string $order_id
     * @return $this
     */
    public function setOrderId($order_id);

    /**
     * @return string|null
     */
    public function getPcBrowserExplain();
    /**
     * @param string $pc_browser_explain
     * @return $this
     */
    public function setPcBrowserExplain($pc_browser_explain);
    /**
     * @return string|null
     */
    public function getMobileBrandExplain();
    /**
     * @param string $mobile_brand_explain
     * @return $this
     */
    public function setMobileBrandExplain($mobile_brand_explain);
    /**
     * @return string|null
     */
    public function getTabletBrandExplain();
    /**
     * @param string $tablet_brand_explain
     * @return $this
     */
    public function setTabletBrandExplain($tablet_brand_explain);
    /**
     * @return string|null
     */
    public function getProblemPageDescription();
    /**
     * @param string $problem_page_description
     * @return $this
     */
    public function setProblemPageDescription($problem_page_description);
    /**
     * @return string|null
     */
    public function getSuggestions();
    /**
     * @param string $suggestions
     * @return $this
     */
    public function setSuggestions($suggestions);


    /**
     * @return string|null
     */
    public function getPcBrowser();
    /**
     * @param string $pc_browser
     * @return $this
     */
    public function setPcBrowser($pc_browser);
    /**
     * @return string|null
     */
    public function getMobileBrand();
    /**
     * @param string $mobile_brand
     * @return $this
     */
    public function setMobileBrand($mobile_brand);
    /**
     * @return string|null
     */
    public function getTabletBrand();
    /**
     * @param string $tablet_brand
     * @return $this
     */
    public function setTabletBrand($tablet_brand);
    /**
     * @return int|null
     */
    public function getLoadingSpeed();
    /**
     * @param int $loading_speed
     * @return $this
     */
    public function setLoadingSpeed($loading_speed);
    /**
     * @return int|null
     */
    public function getUi();
    /**
     * @param int $ui
     * @return $this
     */
    public function setUi($ui);
    /**
     * @return int|null
     */
    public function getSearching();
    /**
     * @param int $searching
     * @return $this
     */
    public function setSearching($searching);
    /**
     * @return int|null
     */
    public function getCategory();
    /**
     * @param int $category
     * @return $this
     */
    public function setCategory($category);
    /**
     * @return int|null
     */
    public function getPromotions();
    /**
     * @param int $promotions
     * @return $this
     */
    public function setPromotions($promotions);
    /**
     * @return int|null
     */
    public function getTextInput();
    /**
     * @param int $text_input
     * @return $this
     */
    public function setTextInput($text_input);
    /**
     * @return int|null
     */
    public function getPaymentMethod();
    /**
     * @param int $payment_method
     * @return $this
     */
    public function setPaymentMethod($payment_method);
    /**
     * @return int|null
     */
    public function getPaymentProcess();
    /**
     * @param int $payment_process
     * @return $this
     */
    public function setPaymentProcess($payment_process);
    /**
     * @return int|null
     */
    public function getStatus();
    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);
    /**
     * @return string|null
     */
    public function getCreatedAt();
    /**
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);

}
