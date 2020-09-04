<?php
namespace Aosom\Marketing\Api\Data;

interface TrialInterface {

    /**
     * Constants for field names.
     */
    const TRIAL_ID = 'trial_id';
    const TITLE = 'title';
    const CUSTOMER_ID = 'customer_id';
    const INCREMENT_ID = 'increment_id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_NAME = 'customer_name';
    const CONTENT = 'content';
    const IMGS = 'imgs';
    const STATUS = 'status';
    const CREATION_AT = 'created_at';
    const UPDATE_AT = 'updated_at';
    const VIDEO = 'video';
    /**
     * @return string|null
     */
    public function getId();

    /**
     * @param string $trial_id
     * @return $this
     */
    public function setTrialId($trial_id);
    /**
     * @return string|null
     */
    public function getTitle();
    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);
    /**
     * @return string|null
     */
    public function getVideo();
    /**
     * @param string $video
     * @return $this
     */
    public function setVideo($video);
    /**
     * @return int|null
     */
    public function getCustomerId();
    /**
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);
    /**
     * @return int|null
     */
    public function getOrderId();
    /**
     * @param int $increment_id
     * @return $this
     */
    public function setOrderId($increment_id);
    /**
     * @return int|null
     */
    public function getProductId();
    /**
     * @param int $product_id
     * @return $this
     */
    public function setProductId($product_id);
    /**
     * @return string|null
     */
    public function getCustomerName();
    /**
     * @param string $customer_name
     * @return $this
     */
    public function setCustomerName($customer_name);
    /**
     * @return string|null
     */
    public function getContent();
    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content);
    /**
     * @return string|null
     */
    public function getImgs();
    /**
     * @param string $imgs
     * @return $this
     */
    public function setImgs($imgs);
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
    /**
     * @return string|null
     */
    public function getUpdatedAt();
    /**
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at);

}
