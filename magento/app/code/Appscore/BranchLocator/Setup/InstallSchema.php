<?php 
namespace Appscore\BranchLocator\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface 
{

    public function install(SchemaSetupInterface $setup,ModuleContextInterface $context)
    {
        $setup->startSetup();
        $conn = $setup->getConnection();
        $tableName = $setup->getTable('appscore_branchlocator_branchlist');
        if($conn->isTableExists($tableName) != true){
            $table = $conn->newTable($tableName)
                            ->addColumn(
                                'id',
                                Table::TYPE_INTEGER,
                                null,
                                ['identity'=>true,'unsigned'=>true,'nullable'=>false,'primary'=>true]
                                )
                            ->addColumn(
                                'name',
                                Table::TYPE_TEXT,
                                255,
                                ['nullable'=>false,'default'=>'']
                                )
                            ->addColumn(
                                'address',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false,'default'=>'']
                                )
                            ->addColumn(
                                'city',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false,'default'=>'']
                                )
                            ->addColumn(
                                'state',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false,'default'=>'']
                                )
                            ->addColumn(
                                'postcode',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false,'default'=>'']
                                )
                            ->addColumn(
                                'phone',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false,'default'=>'']
                                )
                            ->addColumn(
                                'phone',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>true,'default'=>'']
                                )
                            ->addColumn(
                                'monday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'tuesday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'wednesday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'thursday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'friday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'saturday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'sunday',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>false]
                                )
                            ->addColumn(
                                'latitude',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>true,'default'=>'']
                                )
                            ->addColumn(
                                'longitude',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>true,'default'=>'']
                                )
                            ->addColumn(
                                'status',
                                Table::TYPE_BOOLEAN,
                                null,
                                ['identity' => false, 'nullable' => false]
                                )
                            ->addColumn(
                                'country',
                                Table::TYPE_TEXT,
                                255,
                                ['nullable' => false]
                                )
                            ->setOption('charset','utf8');
            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}
 ?>