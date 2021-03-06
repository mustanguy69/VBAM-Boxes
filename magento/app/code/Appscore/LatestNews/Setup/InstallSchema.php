<?php
namespace Appscore\LatestNews\Setup;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\Store;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockInterfaceFactory,
        State $state
    ) {
        $this->blockRepository = $blockRepository;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
        $this->state = $state;
    }

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		try {
			$installer = $setup;
			$installer->startSetup();
			if (!$installer->tableExists('appscore_latestnews_news')) {
				$table = $installer->getConnection()->newTable(
					$installer->getTable('appscore_latestnews_news')
				)
					->addColumn(
						'id',
						\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'nullable' => false,
							'primary'  => true,
							'unsigned' => true,
						],
						'News ID'
					)
					->addColumn(
						'title',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'News Title'
					)
					->addColumn(
						'url_key',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						[],
						'News URL Key'
					)
					->addColumn(
						'short_content',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						'64k',
						[],
						'News Short Content'
					)
					->addColumn(
						'content',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						'64k',
						[],
						'News Content'
					)
					->addColumn(
						'status',
						\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						1,
						[],
						'News Status'
					)
					->addColumn(
						'image',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						[],
						'News Image'
					)
					->addColumn(
						'category_id',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						[],
						'News Category'
					)
					->addColumn(
						'created_at',
						\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
						'Created At'
					)->addColumn(
						'updated_at',
						\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
						'Updated At');
				$installer->getConnection()->createTable($table);
			}

			if (!$installer->tableExists('appscore_latestnews_categories')) {
				$table = $installer->getConnection()->newTable(
					$installer->getTable('appscore_latestnews_categories')
				)
					->addColumn(
						'id',
						\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'nullable' => false,
							'primary'  => true,
							'unsigned' => true,
						],
						'Category ID'
					)
					->addColumn(
						'name',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'Category Name'
					);
				$installer->getConnection()->createTable($table);
			}
			$installer->endSetup();

			$this->state->setAreaCode(Area::AREA_ADMINHTML);
		
			/** @var BlockInterface $cmsBlock */
			$cmsBlock = $this->blockInterfaceFactory->create();

            $content = '';

			$cmsBlock->setIdentifier('about_us');
			$cmsBlock->setTitle('About Us');
			$cmsBlock->setContent($content);
			$cmsBlock->setData('stores', [Store::DEFAULT_STORE_ID]);

			$this->blockRepository->save($cmsBlock);
		} catch (\Exception $e) {
			$this->logger->critical($e->getMessage());
		}
	}
}
