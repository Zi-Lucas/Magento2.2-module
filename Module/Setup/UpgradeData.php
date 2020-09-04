<?php
namespace Aosom\Marketing\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * ElasticsuiteRating Upgrade Data Script.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteRating
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;


    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Eav\Setup\EavSetupFactory          $eavSetupFactory EAV Setup Factory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addProductAttributes($setup,$context);
        }

    }

    private function addProductAttributes($setup,$context){
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entity = \Magento\Catalog\Model\Product::ENTITY;
        $global = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL;
        $product_attributes = [
            'aosom_trial_status' => [
                'type' => 'int',
                'label' => 'Is Trial',
                'input' => 'boolean',
                'backend' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'is_used_in_grid'            => 1,
                'is_visible_in_grid'         => 1,
                'is_filterable_in_grid'      => 1,
                'searchable' => 1,
                'used_for_sort_by' => 1,
                'filterable' => 0,//聚合搜索
                'visible' => 1,
                'required' => false,
                'is_user_defined'=>true,
                'used_in_product_listing' => true,
                'sort_order' => 6,
                'default' => 0,
                'global'=>$global
            ],
            'aosom_trial_sale' => [
                'type' => 'int',
                'label' => 'Trial Sale',
                'input' => 'text',
                'backend' => '',
                'source' => '',
                'is_used_in_grid'            => 1,
                'is_visible_in_grid'         => 1,
                'is_filterable_in_grid'      => 0,
                'searchable' => 0,
                'used_for_sort_by' => 0,
                'filterable' => 0,//聚合搜索
                'visible' => 1,
                'default' => 100,
                'required' => false,
                'is_user_defined'=>true,
                'used_in_product_listing' => true,
                'sort_order' => 7,
                'global'=>$global
            ],
            'aosom_trial_quantity' => [
                'type' => 'int',
                'label' => 'Trial quantity',
                'input' => 'text',
                'backend' => '',
                'source' => '',
                'is_used_in_grid'            => 1,
                'is_visible_in_grid'         => 1,
                'is_filterable_in_grid'      => 0,
                'searchable' => 0,
                'used_for_sort_by' => 0,
                'filterable' => 0,//聚合搜索
                'visible' => 1,
                'required' => false,
                'default' => 0,
                'is_user_defined'=>true,
                'used_in_product_listing' => true,
                'sort_order' => 7,
                'global'=>$global
            ],
        ];
        foreach ($product_attributes as $key=>$value){
            $eavSetup->addAttribute($entity, $key, $value);
            $attributeId = $eavSetup->getAttributeId($entity, $key);
            $defaultAttributeSet = $eavSetup->getAttributeSetId($entity, 'Migration_Default');
            $defaultGroup = $eavSetup->getAttributeGroupId($entity, $defaultAttributeSet, 'Product Details');
            $eavSetup->addAttributeToSet($entity, $defaultAttributeSet, $defaultGroup, $attributeId);
        }
        $setup->endSetup();
    }
}
