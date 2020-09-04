<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Store\Model\ScopeInterface;


/**
 * Create value-object \Magento\Framework\Phrase
 *
 * @return \Magento\Framework\Phrase
 */
function __()
{
    $argc = func_get_args();

    $text = array_shift($argc);
    if (!empty($argc) && is_array($argc[0])) {
        $argc = $argc[0];
    }

    return new \Magento\Framework\Phrase($text, $argc);
}

if(!function_exists('api_return_message'))
{
    /**
     * 接口输出错误信息
     * @param int $error_code
     * @param string $error_msg
     * @param array $data
     * @return false|float|int|mixed|Services_JSON_Error|string|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function api_return_message($error_code=0,$error_msg="",$data=[])
    {
        throw new \Magento\Framework\Exception\LocalizedException(new \Magento\Framework\Phrase($error_msg,$data),null,$error_code);
    }
}
if(!function_exists('new_arrivals_config'))
{
    /**
     * 获取新品券后台配置参数
     * @return array
     */
    function new_arrivals_config()
    {
        $data = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig =$objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        try {
            $enable = $scopeConfig->getValue(
                'aosom_couponrules/general/new_arrivals_enable',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $data['enable'] = $enable;
        }catch (\Exception $e){
            $data['enable'] = 0;
        }
        try {
            $ruleId = $scopeConfig->getValue(
                'aosom_couponrules/general/new_arrivals_rule_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $data['rule_id'] = $ruleId;
        }catch (\Exception $e){
            $data['rule_id'] = 0;
        }
        try {
            $active_time = $scopeConfig->getValue(
                'aosom_couponrules/general/new_arrivals_coupon_time',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $data['active_time'] = $active_time;
        }catch (\Exception $e){
            $data['active_time'] = [];
        }
        try {
            $coupon_limit = $scopeConfig->getValue(
                'aosom_couponrules/general/new_arrivals_coupon_limit',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $data['coupon_limit'] = $coupon_limit;
        }catch (\Exception $e){
            $data['coupon_limit'] = 0;
        }
        return $data;
    }
}

if(!function_exists('get_customer_id'))
{
    /**
     * 获取当前用户ID
     * @return int
     */
    function get_customer_id(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $http = $objectManager->get('\Magento\Framework\App\Request\Http');
        $token = $objectManager->get('\Magento\Integration\Model\Oauth\TokenFactory')->create();
        $authorizationHeader = $http->getHeader('Authorization');
        $tokenParts = explode('Bearer', $authorizationHeader);
        $tokenPayload = trim(array_pop($tokenParts));
        $token->loadByToken($tokenPayload);
        $customerId = $token->getCustomerId() ? $token->getCustomerId() : 0;
        return $customerId;
    }
}
if(!function_exists('get_group_id'))
{
    function get_group_id(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $http = $objectManager->get('\Magento\Framework\App\Request\Http');
        $usergroup = $http->getHeader('usergroup');
        if($usergroup > 0){
            return $usergroup;
        }else{
            $customer_id = get_customer_id();
            $usergroup = 0;
            if(!empty($customer_id)){
                $customerRepository = $objectManager->get(\Magento\Customer\Api\CustomerRepositoryInterface::class);
                $customer = $customerRepository->getById($customer_id);
                $usergroup = $customer->getGroupId();
            }
        }
        return $usergroup;
    }
}
if (!function_exists('m2_object_2_array'))
{
    /**
     * 解析magento Object to array
     * @param $dataObject
     * @param $dataObjectType
     * @return mixed
     */
    function m2_object_2_array($dataObject, $dataObjectType)
    {
        $dataObjectProcessor = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\Reflection\DataObjectProcessor');
        $dataArray = $dataObjectProcessor->buildOutputDataArray($dataObject, $dataObjectType);
        return $dataArray;
    }
}

if (!function_exists('m2_array_to_map'))
{
    function m2_array_to_map($array,$key,$vkey)
    {
        $map = [];
        foreach ($array as $item){
            $map[$item[$key]] =  $item[$vkey];
        }
        return $map;
    }
}
if (!function_exists('product_array2map'))
{
    function product_array2map(&$products,$is_list=false)
    {
        if($is_list) {
            foreach ($products as &$product) {
                if(isset($product['extension_attributes']['common_desc'] ))
                    $product['extension_attributes']['common_desc'] = m2_array_to_map($product['extension_attributes']['common_desc'], 'key', 'value');
                if(isset($product['custom_attributes']))
                    $product['custom_attributes'] = m2_array_to_map($product['custom_attributes'], 'attribute_code', 'value');
            }
        }else{
            if(isset($products['extension_attributes']['common_desc'] ))
                $products['extension_attributes']['common_desc'] = m2_array_to_map($products['extension_attributes']['common_desc'], 'key', 'value');
            if(isset($products['custom_attributes']))
                $products['custom_attributes'] = m2_array_to_map($products['custom_attributes'], 'attribute_code', 'value');
        }
    }
}

if (!function_exists('m2_logger'))
{
    function m2_logger($channel,$message,$filename="",$type = 'notice')
    {
        $filename = empty($filename)?''.date('Ymd').'.log':$filename;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $filename);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }
}

if (!function_exists('m2_date'))
{
    /**
     * 获取当前时间（根据当前时区将UTC转当前时间）
     * @param string $dateType
     * @param $date
     * @return string
     * @throws Exception
     */
    function m2_date($dateType,$timestamp=0)
    {
        $timestamp = $timestamp?$timestamp:time();
        $date = date("Y-m-d H:i:s",$timestamp);
        $dateTime = (new \DateTime($date, new \DateTimeZone('UTC')));
        $backendConfig = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Backend\App\ConfigInterface')
            ->getValue('general/locale/timezone');
        $dateTime->setTimezone(new \DateTimeZone($backendConfig));
        return $dateTime->format($dateType);
    }
}
if (!function_exists('m2_datetoutc'))
{
    /**
     * 根据当前时区转UTC时间
     * @param string $dateType
     * @param $date
     * @return string
     * @throws Exception
     */
    function m2_datetoutc($dateType,$date)
    {
        $backendConfig = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Backend\App\ConfigInterface')
            ->getValue('general/locale/timezone');
        $dateTime = (new \DateTime($date, new \DateTimeZone($backendConfig)));

        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        return $dateTime->format($dateType);
    }
}
if (!function_exists('m2_utcdate'))
{
    /**
     * 获取UTC时间
     * @param string $dateType
     * @param $date
     * @return string
     * @throws Exception
     */
    function m2_utcdate($includeTime = true)
    {
        $dateTime = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Stdlib\DateTime');
        $date = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Stdlib\DateTime\DateTime');
        return $dateTime->formatDate($date->gmtTimestamp(),$includeTime);
    }
}

if (!function_exists('m2_monolog'))
{
    /**
     * Monolog log channel
     *
     * It contains a stack of Handlers and a stack of Processors,
     * and uses them to store records that are added to it.
     * @param $channelName
     * @param string $logFile
     * @return \Monolog\Logger
     * @throws Exception
     * @author Jordi Boggiano <j.boggiano@seld.be>
     */
    function m2_monolog($channelName,$logFile=  'm2_monolog.log')
    {
        $logFile = BP. '/var/log/' .$logFile;
        $logger = new \Monolog\Logger($channelName);
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($logFile));
        return $logger;
    }
}

if (!function_exists('trial_is_enabled'))
{
    function trial_is_enabled()
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get('\Aosom\Marketing\Helper\Data');
        return $helper->trialIsEnabled();
    }
}
