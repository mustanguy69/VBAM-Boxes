<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Shell
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Shell\Model\Manual;

use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\Config\DependencyInfoProvider;
use Magento\Framework\Indexer\ConfigInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\StateInterface;
use Magento\Indexer\Console\Command\AbstractIndexerManageCommand;
use Magento\Indexer\Model\IndexerFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Visy\Shell\Helper\Data as ShellHelper;

class Reindex extends AbstractIndexerManageCommand
{
    /**
     * Shared Indexer array
     *
     * @var []
     */
    private $sharedIndexesComplete = [];

    /**
     * Config Interface
     *
     * @var \Magento\Framework\Indexer\ConfigInterface
     */
    private $config;
    /**
     * @var IndexerFactory
     */
    private $_indexerFactory;

    /**
     * @var OutputInterface
     */
    private $_output;

    /**
     * @var ShellHelper
     */
    protected $helper;

    /**
     * @var DependencyInfoProvider|null
     */
    private $dependencyInfoProvider;
    /**
     * @var JsonFactory
     */
    protected $_jsonFactory;

    /**
     * Reindex constructor.
     * @param ObjectManagerFactory $objectManagerFactory
     * @param IndexerFactory $indexerFactory
     * @param ShellHelper $helper
     */
    public function __construct(
        ObjectManagerFactory $objectManagerFactory,
        IndexerFactory $indexerFactory,
        JsonFactory $jsonFactory,
        ShellHelper $helper
    ) {
        $this->_indexerFactory = $indexerFactory;
        $this->helper = $helper;
        $this->_jsonFactory = $jsonFactory;
        parent::__construct($objectManagerFactory);
    }

    /**
     * This method will execute the re-indexer
     *
     * @param string $indexerId
     * @param OutputInterface $output
     * @param bool $flag
     * @return Json
     * @throws \Throwable
     */
    public function reindexById($indexerId, OutputInterface $output, $flag = true)
    {
        $this->_output = $output;
        $resultJson = $this->_jsonFactory->create();
        $returnMsg = '';
        $error = false;
        foreach ($this->getSpecificIndexers($indexerId) as $indexer) {
            try {
                $this->validateIndexerStatus($indexer);
                $startTime = microtime(true);
                $indexerConfig = $this->getConfig()->getIndexer($indexer->getId());
                $sharedIndex = $indexerConfig['shared_index'];

                // Skip indexers having shared index that was already complete
                if (!in_array($sharedIndex, $this->sharedIndexesComplete)) {
                    $indexer->reindexAll();
                    if ($sharedIndex) {
                        $this->validateSharedIndex($sharedIndex);
                    }
                }
                $resultTime = microtime(true) - $startTime;
                $message = $indexer->getTitle() . ' index has been rebuilt successfully in ' . gmdate('H:i:s', $resultTime);
                $returnMsg = $returnMsg . '<div>' . $message . '</div>';
                $this->helper->logIndexer('info', $message, $output, $flag);
            } catch (LocalizedException $e) {
                $error = true;
                $message = '(Localized Exception) :' . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
                $returnMsg = $returnMsg . '<div>' . $e->getMessage() . '</div>';
                $this->helper->logIndexer('error', $message, $output, $flag);
                $message = $this->reIndexByIdIndexerSpecific($indexer->getId(), $output, $flag);
                $returnMsg = $returnMsg . '<div>' . $message . '</div>';
            } catch (\Exception $e) {
                $error = true;
                $message = $indexer->getTitle() . ' indexer process Unknown Exception:' .
                    $e->getMessage() . PHP_EOL . $e->getTraceAsString();
                $returnMsg = $returnMsg . '<div>' . $e->getMessage() . '</div>';
                $this->helper->logIndexer('error', $message, $output, $flag);
            }
        }

        //Clear Config Value
        if ($flag) {
            $this->helper->saveToStoreConfig(ShellHelper::CONFIG_INTEGRATION_INDEXER_TYPE, null);
        }

        return $resultJson->setData(['errors' => $error, 'message' => $returnMsg]);
    }

    /**
     *  ReIndex By Id (Only the specific indexer)
     *
     * @param $indexerId
     * @param OutputInterface $output
     * @param bool $flag
     * @return string
     * @throws \Throwable
     */
    public function reIndexByIdIndexerSpecific($indexerId, OutputInterface $output, $flag = true)
    {
        $this->_output = $output;
        $returnMsg = '';
        if ($indexerId) {
            $indexer = $this->_indexerFactory->create();
            $indexer->load($indexerId);
            $message = $this->resetIndexes($indexer, $output, $flag);
            $returnMsg = $returnMsg . $message . PHP_EOL;

            try {
                $this->validateIndexerStatus($indexer);
                $startTime = microtime(true);
                $sharedIndex = $indexer['shared_index'];

                // Skip indexers having shared index that was already complete
                if (!in_array($sharedIndex, $this->sharedIndexesComplete)) {
                    $indexer->reindexAll();
                    if ($sharedIndex) {
                        $this->validateSharedIndex($sharedIndex);
                    }
                }

                $resultTime = microtime(true) - $startTime;
                $message = $indexer->getTitle() . ' index has been rebuilt successfully in ' . gmdate('H:i:s', $resultTime);
                $returnMsg = $returnMsg . '<div>' . $message . '</div>';
                $this->helper->logIndexer('info', $message, $output, $flag);
            } catch (LocalizedException $e) {
                $message = '(Localized Exception) :' . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
                $returnMsg = $returnMsg . '<div>' . $e->getMessage() . '</div>';
                $this->helper->logIndexer('error', $message, $output, $flag);
            } catch (\Exception $e) {
                $message = $indexer->getTitle() . ' indexer process unknown error:' .
                    $e->getMessage() . PHP_EOL . $e->getTraceAsString();
                $returnMsg = $returnMsg . '<div>' . $e->getMessage() . '</div>';
                $this->helper->logIndexer('error', $message, $output, $flag);
            }
        }
        return $returnMsg;
    }

    /**
     * Validate that indexer is not locked
     *
     * @param IndexerInterface $indexer
     * @return void
     * @throws LocalizedException
     */
    private function validateIndexerStatus(IndexerInterface $indexer)
    {
        if ($indexer->getStatus() == StateInterface::STATUS_WORKING) {
            throw new LocalizedException(
                __(
                    '%1 index is locked by another reindex process. Skipping.',
                    $indexer->getTitle()
                )
            );
        }
    }

    /**
     * Get Config
     *
     * @return \Magento\Framework\Indexer\ConfigInterface
     * @deprecated
     */
    private function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->getObjectManager()->get(ConfigInterface::class);
        }
        return $this->config;
    }

    /**
     * Validate indexers by shared index ID
     *
     * @param string $sharedIndex
     * @return $this
     * @throws \Exception
     */
    private function validateSharedIndex(
        $sharedIndex
    ) {
        if (empty($sharedIndex)) {
            throw new \InvalidArgumentException('sharedIndex must be a valid shared index identifier');
        }
        $indexerIds = $this->getIndexerIdsBySharedIndex($sharedIndex);
        if (empty($indexerIds)) {
            return $this;
        }
        $indexerFactory = $this->getObjectManager()->create(\Magento\Indexer\Model\IndexerFactory::class);
        foreach ($indexerIds as $indexerId) {
            /** @var \Magento\Indexer\Model\Indexer $indexer */
            $indexer = $indexerFactory->create();
            $indexer->load($indexerId);
            /** @var \Magento\Indexer\Model\Indexer\State $state */
            $state = $indexer->getState();
            $state->setStatus(StateInterface::STATUS_VALID);
            $state->save();
        }
        $this->sharedIndexesComplete[] = $sharedIndex;
        return $this;
    }

    /**
     * Get indexer ids that have common shared index
     *
     * @param string $sharedIndex
     * @return []
     */
    private function getIndexerIdsBySharedIndex(
        $sharedIndex
    ) {
        $indexers = $this->getConfig()->getIndexers();
        $result = [];
        foreach ($indexers as $indexerConfig) {
            if ($indexerConfig['shared_index'] == $sharedIndex) {
                $result[] = $indexerConfig['indexer_id'];
            }
        }
        return $result;
    }

    /**
     * Reset Indexes
     * @param $indexer
     * @param $output
     * @param bool $flag
     * @return string
     */
    private function resetIndexes($indexer, $output, $flag = true)
    {
        $returnMsg = '';
        try {
            $indexer->getState()
                ->setStatus(StateInterface::STATUS_INVALID)
                ->save();
            $message = $indexer->getTitle() . ' indexer has been invalidated.';
            $returnMsg = $returnMsg . '<div>' . $message . '</div>';
            $this->helper->logIndexer('info', $message, $output, $flag);
        } catch (\Exception $e) {
            $message = $indexer->getTitle() . ' indexer has been not invalidated.' . PHP_EOL
                . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
            $returnMsg = $returnMsg . '<div>' . $e->getMessage() . '</div>';
            $this->helper->logIndexer('error', $message, $output, $flag);
        }
        return $returnMsg;
    }

    /**
     * Get Specific Indexers
     * (Extracted from Magento\Indexer\Console\Command\IndexerReindexCommand)
     * @inheritdoc
     *
     * Returns the ordered list of specified indexers and related indexers.
     */
    protected function getSpecificIndexers($indexerId)
    {
        $requestedTypes = [];
        if ($indexerId!='*') {
            $requestedTypes = [ 0 => $indexerId ];
        }
        $indexers =  $this->getOrderedIndexers($requestedTypes);
        $allIndexers = $this->getAllIndexers();
        if (!array_diff_key($allIndexers, $indexers)) {
            return $indexers;
        }

        $relatedIndexers = [];
        $dependentIndexers = [];
        foreach ($indexers as $indexer) {
            $relatedIndexers = array_merge(
                $relatedIndexers,
                $this->getRelatedIndexerIds($indexer->getId())
            );
            $dependentIndexers = array_merge(
                $dependentIndexers,
                $this->getDependentIndexerIds($indexer->getId())
            );
        }

        $invalidRelatedIndexers = [];
        foreach (array_unique($relatedIndexers) as $relatedIndexer) {
            if ($allIndexers[$relatedIndexer]->isInvalid()) {
                $invalidRelatedIndexers[] = $relatedIndexer;
            }
        }

        return array_intersect_key(
            $allIndexers,
            array_flip(
                array_unique(
                    array_merge(
                        array_keys($indexers),
                        $invalidRelatedIndexers,
                        $dependentIndexers
                    )
                )
            )
        );
    }

    /**
     * Return all indexer Ids on which the current indexer depends (directly or indirectly).
     *
     * @param string $indexerId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRelatedIndexerIds(string $indexerId)
    {
        $relatedIndexerIds = [];
        foreach ($this->getDependencyInfoProvider()->getIndexerIdsToRunBefore($indexerId) as $relatedIndexerId) {
            $relatedIndexerIds = array_merge(
                $relatedIndexerIds,
                [$relatedIndexerId],
                $this->getRelatedIndexerIds($relatedIndexerId)
            );
        }

        return array_unique($relatedIndexerIds);
    }

    /**
     * Return all indexer Ids which depend on the current indexer (directly or indirectly).
     *
     * @param string $indexerId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getDependentIndexerIds(string $indexerId)
    {
        $dependentIndexerIds = [];
        foreach (array_keys($this->getConfig()->getIndexers()) as $id) {
            $dependencies = $this->getDependencyInfoProvider()->getIndexerIdsToRunBefore($id);
            if (array_search($indexerId, $dependencies) !== false) {
                $dependentIndexerIds = array_merge(
                    $dependentIndexerIds,
                    [$id],
                    $this->getDependentIndexerIds($id)
                );
            }
        }

        return array_unique($dependentIndexerIds);
    }

    /**
     * Get dependency info provider
     *
     * @return DependencyInfoProvider
     * @deprecated 100.2.0
     */
    private function getDependencyInfoProvider()
    {
        if (!$this->dependencyInfoProvider) {
            $this->dependencyInfoProvider = $this->getObjectManager()->get(DependencyInfoProvider::class);
        }
        return $this->dependencyInfoProvider;
    }

    /**
     * Returns the ordered list of indexers.
     *
     * @param InputInterface $input
     * @return IndexerInterface[]
     * @throws \InvalidArgumentException
     */
    protected function getOrderedIndexers($requestedTypes)
    {
        if (empty($requestedTypes)) {
            $indexers = $this->getAllIndexers();
        } else {
            $availableIndexers = $this->getAllIndexers();
            $unsupportedTypes = array_diff($requestedTypes, array_keys($availableIndexers));
            if ($unsupportedTypes) {
                throw new \InvalidArgumentException(
                    "The following requested index types are not supported: '" . join("', '", $unsupportedTypes)
                    . "'." . PHP_EOL . 'Supported types: ' . join(", ", array_keys($availableIndexers))
                );
            }
            $indexers = array_intersect_key($availableIndexers, array_flip($requestedTypes));
        }

        return $indexers;
    }
}
