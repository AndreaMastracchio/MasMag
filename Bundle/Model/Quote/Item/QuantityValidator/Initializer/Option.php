<?php

namespace MasMag\Bundle\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\Quote\Model\Quote\Item;

class Option extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option
{
    public function __construct(QuoteItemQtyList $quoteItemQtyList, StockRegistryInterface $stockRegistry, StockStateInterface $stockState)
    {
        parent::__construct($quoteItemQtyList, $stockRegistry, $stockState);
    }

    public function initialize(
        Item\Option $option,
        Item        $quoteItem,
                    $qty
    )
    {
        $optionValue = $option->getValue();
        $optionQty = $qty * $optionValue;
        $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $optionValue;
        $qtyForCheck = $this->quoteItemQtyList->getQty(
            $option->getProduct()->getId(),
            $quoteItem->getId(),
            $quoteItem->getQuoteId(),
            $increaseOptionQty
        );

        $stockItem = $this->getStockItem($option, $quoteItem);
        $stockItem->setProductName($option->getProduct()->getName());
        if ($quoteItem->getProductType() !== 'bundle' && $quoteItem->getProductType() !== 'configurable') {
            $result = $this->stockState->checkQuoteItemQty(
                $option->getProduct()->getId(),
                $optionQty,
                $qtyForCheck,
                $optionValue,
                $option->getProduct()->getStore()->getWebsiteId()
            );
        } else {
            $result = $this->stockState->checkQuoteItemQty(
                $option->getProduct()->getId(),
                $stockItem->getMinSaleQty(),
                $qtyForCheck,
                $optionValue,
                $option->getProduct()->getStore()->getWebsiteId()
            );
        }

        if ($result->getItemIsQtyDecimal() !== null) {
            $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
        }

        if ($result->getHasQtyOptionUpdate()) {
            $option->setHasQtyOptionUpdate(true);
            $quoteItem->updateQtyOption($option, $result->getOrigQty());
            $option->setValue($result->getOrigQty());
            /**
             * if option's qty was updates we also need to update quote item qty
             */
            $quoteItem->setData('qty', (int)$qty);
        }
        if ($result->getMessage() !== null) {
            $option->setMessage($result->getMessage());
            $quoteItem->setMessage($result->getMessage());
        }
        if ($result->getItemBackorders() !== null) {
            $option->setBackorders($result->getItemBackorders());
        }

        $stockItem->unsIsChildItem();

        $option->setStockStateResult($result);

        return $result;
    }
}
