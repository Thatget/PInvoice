<?php

namespace MW\PInvoice\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface{

    /**
     * @inheritDoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable($installer->getTable('mw_pinvoice_invoice_payment'))
            ->addColumn(
                'ip_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['primary' => true, 'identity' => true, 'nullable' => false],
                'Invoice Payment ID'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Order ID'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                3,
                [
                    'nullable' => true,
                    'default' => 0
                ],
                'Status'
            )
            ->addColumn(
                'amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '20,4',
                [],
                'amount money'
            )->addColumn(
                'create_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Timestamp'
            )->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => ''],
                'Payment Comment'
            )->setComment('Payment Invoice table');
        $setup->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
