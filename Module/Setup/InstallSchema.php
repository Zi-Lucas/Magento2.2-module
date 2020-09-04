<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2018/8/28
 * Time: 14:10
 */

namespace Aosom\Marketing\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'aosom_trial_report'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aosom_trial_report')
        )->addColumn(
            'trial_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['identity' => false, 'nullable' => false, 'primary' => true],
            'Trial Report ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            200,
            ['nullable' => true],
            'Trial Report Title'
        )->addColumn(
            'video',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => true],
            'Trial Report Video Link'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Trial Report Title'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            32,
            ['nullable' => false],
            'Trial Report customer_id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false],
            'Product Id'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'Trial Report increment_id'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Trial Report Feel'
        )->addColumn(
            'imgs',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1024,
            ['nullable' => true],
            'Trial Report Img Feel'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Trial Report Created Time'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Trial Report Updated Time'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Trial Report Status'
        )->addIndex(
            $installer->getIdxName('aosom_trial_report', ['increment_id'],AdapterInterface::INDEX_TYPE_UNIQUE),
            ['increment_id']
        )->addIndex(
            $installer->getIdxName('aosom_trial_report', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('aosom_trial_report', ['product_id']),
            ['product_id']
        )->setComment(
            'Trial Report Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
