<?php

namespace Appscore\ABNCheck\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Visy\BNCNLookup\Model\Api\Lookup;

class Check extends Action
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
            $validity = $this->_lookup->checkValidity('92143315066');

            if ($validity == true) {
                return $this->resultJsonFactory->create()
                    ->setData([ 'success' => 'OK', 'access_token' => $validity, 'message' =>'Valid ABN/ACN ' . '92143315066']);
            } else {
                return $this->resultJsonFactory->create()->setData($validity);
                //return $this->resultJsonFactory->create()->setData([ 'success' => 'NO', 'message' =>'Invalid ABN/ACN ' . '92143315066' ]);
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
