<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Shell
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Shell\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Visy\Shell\Helper\Data as ShellHelper;
use Visy\Shell\Model\Manual\Reindex;

class Run extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var ShellHelper
     */
    protected $helper;
    /**
     * @var Reindex
     */
    protected $_reindex;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     * @param ShellHelper $helper
     * @param Reindex $reindex
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ShellHelper $helper,
        Reindex $reindex
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->_reindex = $reindex;

        parent::__construct($context);
    }

    /**
     * Reindex Result
     *
     * @return Json @return \Magento\Framework\DataObject
     */
    public function execute()
    {
        try {
            $output = new ConsoleOutput();
            $this->helper->logIndexer('info', 'Started executing Manual Indexer Process', $output, false);
            $indexerId = $this->helper->getConfigValue(ShellHelper::CONFIG_MANUAL_INDEXER_TYPE);
            $reindexResult = $this->_reindex->reindexById($indexerId, $output, false);
            $this->helper->logIndexer('info', 'End executing Manual Indexer Process' . PHP_EOL, $output, false);

            return $reindexResult;
        } catch (\Exception $e) {
            $message = 'Error :' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
            $this->helper->logIndexer('error', $message, $output, false);
            return $this->resultJsonFactory
                ->create()
                ->setData([ 'errors' => false, 'message' => 'Technical Error has occurred. Please Try again later.' ]);
        } catch (\Throwable $e) {
            $message = 'Threw an error :' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
            $this->helper->logIndexer('error', $message, $output, false);
            return $this->resultJsonFactory
                ->create()
                ->setData([ 'errors' => false, 'message' => 'Threw a Technical Error. Please Try again later.' ]);
        }
    }
}
