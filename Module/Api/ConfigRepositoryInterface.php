<?php
namespace Aosom\Marketing\Api;

interface ConfigRepositoryInterface
{
    /**
     * @return \Magento\Framework\DataObject
     */
    public function getConfigInfo();

}
