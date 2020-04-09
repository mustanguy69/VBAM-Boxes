<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Shell
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Shell\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Data
 * @package Visy\Shell\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_INTEGRATION_INDEXER_TYPE = 'visy_integration/pricing/indexer';
    const ENABLE_LOG = 'visy_integration/pricing/enable_indexer_log';
    const CONFIG_MANUAL_INDEXER_TYPE = 'admin/reindex/indexer';
    const LOG_FILE_INDEXER = "/visy_integration_indexer.log";
    const LOG_MANUAL_INDEXER = "/visy_manual_indexer.log";
    /**
     * @var DirectoryList
     */
    private $_directoryList;
    /**
     * @var OutputInterface
     */
    private $_output;
    /**
     * @var WriterInterface
     */
    private $storeWrite;
    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        WriterInterface $writer,
        TypeListInterface $cacheTypeList,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->_directoryList = $directoryList;
        $this->storeWrite        = $writer;
        $this->cacheTypeList = $cacheTypeList;
        $this->_scopeConfig = $scopeConfig;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Log Indexer Activities
     * @param $status
     * @param $message
     * @param OutputInterface $output
     * @param bool $flag
     */
    public function logIndexer($status, $message, OutputInterface $output, $flag = true)
    {
        $this->_output = $output;
        $datetime = date("Y-m-d H:i:sA T");
        $this->_output->writeln("[{$datetime}][{$status}] {$message}");
        try {
            if ($this->getConfigValue(self::ENABLE_LOG)) {
                file_put_contents(
                    $this->_directoryList->getPath('log') .
                    (($flag) ? (self::LOG_FILE_INDEXER) : (self::LOG_MANUAL_INDEXER)),
                    "[$datetime][$status]" . $message . PHP_EOL,
                    FILE_APPEND
                );
            }
        } catch (FileSystemException $e) {
            $this->logger->error("Error:" . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    /**
     * Save to Store Config
     * @param $path
     * @param $value
     */
    public function saveToStoreConfig($path, $value)
    {
        $this->storeWrite->save($path, $value);
        $this->cacheTypeList->cleanType('config');
    }

    /**
     * Get Config Value
     * @param $value
     * @return mixed
     */
    public function getConfigValue($value)
    {
        return $this->_scopeConfig->getValue(
            $value,
            ScopeInterface::SCOPE_STORES
        );
    }
}
