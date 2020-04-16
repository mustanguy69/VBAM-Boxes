<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Checkout\Setup;

/**
 * Description of UpgradeSchema
 */

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '3.3.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'customer_code',
                [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'SAP Customer Code'
                    ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'customer_code',
                [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'SAP Customer Code'
                    ]
            );
        }

        if (version_compare($context->getVersion(), '3.3.2') < 0) {

            $setup->getConnection()->dropColumn(
                $setup->getTable('quote'),
                'delivery_date'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('sales_order'),
                'delivery_date'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('sales_order_grid'),
                'delivery_date'
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'delivery_date',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery Date',
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'delivery_date',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery Date',
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'delivery_date',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery Date',
                ]
            );

            $setup->getConnection()->dropColumn(
                $setup->getTable('quote'),
                'customer_code'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('sales_order'),
                'customer_code'
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'customer_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 32,
                    'default' => null,
                    'comment' => 'SAP Customer Code'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'customer_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 32,
                    'default' => null,
                    'comment' => 'SAP Customer Code'
                ]
            );
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'sap_sent_date',
            [
                'type' => 'datetime',
                'nullable' => true,
                'default' => null,
                'comment' => 'SAP Sent Date'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'sap_sent_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 16,
                'nullable' => true,
                'default' => null,
                'comment' => 'SAP Sent Status'
            ]
        );

        if (version_compare($context->getVersion(), '3.3.3') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'delivery_comment',
                [
                    'type' => 'text',
                    'nullable' => false,
                    'comment' => 'Delivery Comment',
                ]
            );

            $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'delivery_comment',
                [
                    'type' => 'text',
                    'nullable' => false,
                    'comment' => 'Delivery Comment',
                ]
            );
        }

        if (version_compare($context->getVersion(), '3.3.4') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'click_and_collect_id',
                [
                    'type' => 'text',
                    'nullable' => false,
                    'comment' => 'click_and_collect store id',
                ]
            );

            $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'click_and_collect_id',
                [
                    'type' => 'text',
                    'nullable' => false,
                    'comment' => 'click_and_collect store id',
                ]
            );
        }

        $setup->endSetup();
    }
}
