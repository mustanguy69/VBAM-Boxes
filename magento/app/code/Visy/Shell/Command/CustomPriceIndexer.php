<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Shell
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Shell\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Visy\Shell\Helper\Data as ShellHelper;
use Visy\Shell\Model\Jobs\Indexer;

/**
 * Class IntegrationSalesOrderCommand
 */
class CustomPriceIndexer extends Command
{
    private $_state;
    private $_output;
    private $_isRun;
    private $_cronId;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Indexer
     */
    private $indexer;
    /**
     * @var ShellHelper
     */
    protected $helper;

    /**
     * CustomPriceIndexer constructor.
     * @param State $state
     * @param LoggerInterface $logger
     * @param Indexer $indexer
     * @param ShellHelper $helper
     */
    public function __construct(
        State $state,
        LoggerInterface $logger,
        Indexer $indexer,
        ShellHelper $helper
    ) {
        $this->_state = $state;
        $this->logger = $logger;
        $this->indexer = $indexer;
        $this->helper = $helper;
        $this->_cronId = $helper->getConfigValue(ShellHelper::CONFIG_INTEGRATION_INDEXER_TYPE);
        $this->_isRun = (trim($this->_cronId) !== null && trim($this->_cronId) !== '');

        parent::__construct();
    }

    protected function configure()
    {
        // set command name & description.
        $this->setName('visy:integration:custom-pricing-indexer')->setDescription('Visy Custom Pricing Indexer');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->_isRun) {
            $this->_output = $output;

            try {
                $this->_state->emulateAreaCode(
                    Area::AREA_CRONTAB,
                    [$this, "executeIndexer"],
                    [$input, $output]
                );
            } catch (\Exception $e) {
                $this->logger->error("Error executing visy:integration:custom-indexer :" . $e->getMessage());
                $this->logger->error($e->getTraceAsString());
            }
        }
    }

    public function executeIndexer(InputInterface $input, OutputInterface $output)
    {
        $this->helper->logIndexer("info", "Started executing Custom Pricing Indexer Process", $output);
        $this->indexer->executeReindexById($this->_cronId, $output);
    }
}
