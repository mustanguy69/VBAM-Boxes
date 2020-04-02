<?php

namespace Visy\Checkout\Ui\Component\Listing\Column;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class DeliveryDate extends Column
{
    /**
     * @var TimezoneInterface
     */
    protected $_timezone;
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteria;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_timezone = $timezone;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $order  = $this->_orderRepository->get($items["entity_id"]);
                $sapSentDate = $order->getData("sap_sent_date");
                $customerCode = $order->getData("customer_code");
                $sapSentStatus = $order->getData("sap_sent_status");
                $items['customer_code'] = $customerCode;
                $items['sap_sent_status'] = $sapSentStatus;

                if ($items['delivery_date'] == null) {
                    $items['delivery_date'] = 'N/A';
                } else {
                    $formattedDate = $this->_timezone->formatDateTime(
                        $items['delivery_date'],
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::NONE,
                        null,
                        $this->_timezone->getConfigTimezone(
                            ScopeInterface::SCOPE_STORE,
                            0
                        )
                    );
                    $items['delivery_date'] = $formattedDate;
                }

                if ($sapSentDate == null) {
                    $items['sap_sent_date'] = 'N/A';
                } else {
                    $formattedDate = $this->_timezone->formatDateTime(
                        $sapSentDate,
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::MEDIUM,
                        null,
                        $this->_timezone->getConfigTimezone(
                            ScopeInterface::SCOPE_STORE,
                            0
                        )
                    );
                    $items['sap_sent_date'] = $formattedDate;
                }
            }
        }
        return $dataSource;
    }
}
