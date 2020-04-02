<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     BNCNLookup
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\BNCNLookup\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Visy\BNCNLookup\Model\Api\Lookup;

class Test extends Action
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var Lookup
     */
    protected $_lookup;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     * @param Lookup $lookup
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
        Lookup $lookup
    ) {
        $this->_logger           = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_lookup               = $lookup;
        parent::__construct($context);
    }

    /**
     * Test Result
     *
     * @return \Magento\Framework\Controller\Result\Json @return \Magento\Framework\DataObject
     */
    public function execute()
    {
        try {
            $validity = $this->_lookup->checkValidity();

            if ($validity == true) {
                return $this->resultJsonFactory->create()
                    ->setData([ 'success' => 'OK', 'access_token' => $validity, 'message' =>'Valid ABN/ACN' ]);
            } else {
                return $this->resultJsonFactory->create()->setData([ 'success' => 'NO', 'message' =>'Invalid ABN/ACN' ]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process your request right now.')
            );

            return $this->resultJsonFactory
                ->create()
                ->setData([ 'success' => 'NO', 'message' => 'Technical Error has occurred. Please Try again later.' ]);
        }
    }
}
