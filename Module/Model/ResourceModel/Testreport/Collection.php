<?php
namespace Aosom\Marketing\Model\ResourceModel\Testreport;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected $storeManager;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init('Aosom\Marketing\Model\Testreport', 'Aosom\Marketing\Model\ResourceModel\Testreport');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'status');
    }

    /**
     * @inheritdoc
     */
    public function setDateFilter($date)
    {
        $this->addFieldToFilter('created_at', array(
                array('lteq' => $date),
                array('null' => true))
        );
        return $this;
    }
}
