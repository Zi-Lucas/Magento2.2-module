<?php
namespace Aosom\Marketing\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->upgradeSalesOrder($setup,$context);
        $this->upgradeQuote($setup,$context);
        $this->upgradeSalesOrderItem($setup,$context);
        $this->upgradeSalesOrderGrid($setup,$context);
    }

    public function upgradeSalesOrderItem(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.5', '<'))
        {
            $installer->startSetup();
            $tableName = $setup->getTable('sales_order_item');
            if ($setup->getConnection()->isTableExists($tableName) == true)
            {
                $installer->getConnection()
                    ->addColumn($installer->getTable('sales_order_item'),'is_trial', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'length' => 1,
                        'default' => 0,
                        'comment' => 'Is Trial'
                    ));
                $installer->getConnection()
                    ->addColumn($installer->getTable('sales_order_item'),'trial_sale', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'length' => 3,
                        'default' => 0,
                        'comment' => 'Trial Sale'
                    ));
            }
        }

        $installer->endSetup();
    }

    public function upgradeSalesOrder(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.5', '<'))
        {
            $installer->startSetup();
            $tableName = $setup->getTable('sales_order');
            if ($setup->getConnection()->isTableExists($tableName) == true)
            {
                $installer->getConnection()
                    ->addColumn($installer->getTable('sales_order'),'have_trial', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'default' => 0,
                        'comment' => 'Have Trial'
                    ));
            }
        }

        $installer->endSetup();
    }

    public function upgradeSalesOrderGrid(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.6', '<'))
        {
            $installer->startSetup();
            $tableName = $setup->getTable('sales_order_grid');
            if ($setup->getConnection()->isTableExists($tableName) == true)
            {
                $installer->getConnection()
                    ->addColumn($installer->getTable('sales_order_grid'),'have_trial', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'default' => 0,
                        'comment' => 'Have Trial'
                    ));
            }
        }

        $installer->endSetup();
    }

    public function upgradeQuote(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.5', '<'))
        {
            $installer->startSetup();
            $tableName = $setup->getTable('quote_item');
            if ($setup->getConnection()->isTableExists($tableName) == true)
            {
                $installer->getConnection()
                    ->addColumn($installer->getTable('quote_item'),'is_trial', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'length' => 1,
                        'default' => 0,
                        'comment' => 'Is Trial'
                    ));
                $installer->getConnection()
                    ->addColumn($installer->getTable('quote_item'),'trial_sale', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'length' => 3,
                        'default' => 0,
                        'comment' => 'Trial Sale'
                    ));
            }
        }

        $installer->endSetup();
    }
}
