<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Shell
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\Shell\Model\Jobs;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Visy\Shell\Helper\Data as ShellHelper;
use Visy\Shell\Model\Manual\Reindex;

class Indexer
{
    /**
     * @var Reindex
     */
    private $_manualReindex;
    /**
     * @var ShellHelper
     */
    protected $helper;

    /**
     * Indexer constructor.
     * @param Reindex $manualReindex
     * @param ShellHelper $helper
     */
    public function __construct(
        Reindex $manualReindex,
        ShellHelper $helper
    ) {
        $this->_manualReindex = $manualReindex;
        $this->helper = $helper;
    }

    /**
     * Execute Reindex By Id
     * @param $indexerId
     * @param OutputInterface $output
     */
    public function executeReindexById($indexerId, OutputInterface $output)
    {
        try {
            $this->_manualReindex->reindexById($indexerId, $output);
        } catch (\Throwable $e) {
            $message = '(Exception)' . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
            $this->helper->logIndexer("error", $message, $output);
        }
        $this->helper->logIndexer("info", "End executing Custom Pricing Indexer Process" . PHP_EOL, $output);
    }
}
