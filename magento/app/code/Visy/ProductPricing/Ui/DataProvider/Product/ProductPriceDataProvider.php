<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\ProductPricing\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Visy\ProductPricing\Model\ResourceModel\ProductPrice\Product\CollectionFactory;
use Visy\ProductPricing\Model\ResourceModel\ProductPrice\Product\Collection;
use Visy\ProductPricing\Model\ProductPrice;

/**
 * Class ProductPricingDataProvider
 *
 * @api
 *
 * @method Collection getCollection
 * @since 100.1.0
 */
class ProductPriceDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     * @since 100.1.0
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     * @since 100.1.0
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function getData()
    {
        $this->getCollection()->addEntityFilter($this->request->getParam('current_product_id', 0))
            ->addStoreData();

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();

        if (in_array($field, ['customer_price_id', 'customer_code',
            'product_sku', 'qty_n', 'price_1', 'price_n', 'qty_1',])) {
            $filter->setField('rt.' . $field);
        }

        if ($field === 'created_at') {
            $filter->setField('rt.created_at');
        }

        if ($field === 'updated_at') {
            $filter->setField('rt.updated_at');
        }

        parent::addFilter($filter);
    }
}
