<?php

/**
 *
 * Class ProductList
 * @package MasMag\FreeProduct\Model\Config
 *
 * @author Andrea Gregorio Mastracchio
 * @email <andreamastracchio@live.com>
 *
 */


namespace MasMag\FreeProduct\Model\Config;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class ProductList implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name');

        $product_array = [];
        foreach ($collection as $product) {
            $product_array[] = [
                'value' => $product->getSku(),
                'label' => $product->getSku(),
            ];
        }

        return $product_array;
    }
}
