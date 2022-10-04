<?php

namespace MasMag\Bundle\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item;

class StockItem extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem
{

    public function __construct(ConfigInterface $typeConfig, QuoteItemQtyList $quoteItemQtyList, StockStateInterface $stockState, StockStateProviderInterface $stockStateProvider = null)
    {
        parent::__construct($typeConfig, $quoteItemQtyList, $stockState, $stockStateProvider);
    }

    public function initialize(
        StockItemInterface $stockItem,
        Item               $quoteItem,
                           $qty
    )
    {
        $product = $quoteItem->getProduct();
        $quoteItemId = $quoteItem->getId();
        $quoteId = $quoteItem->getQuoteId();
        $productId = $product->getId();
        /**
         * When we work with subitem
         */
        if ($quoteItem->getParentItem()) {
            if ($quoteItem->getParentItem()->getProductType() === 'bundle'
                || $quoteItem->getParentItem()->getProductType() === 'configurable'
            ) {
                if ($stockItem->getMinSaleQty()) {
                    $rowQty = $stockItem->getMinSaleQty();
                    $qtyForCheck = $stockItem->getMinSaleQty();
                } else {
                    $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
                    $qtyForCheck = $this->quoteItemQtyList
                        ->getQty($productId, $quoteItemId, $quoteId, 0);
                }
            } else {
                $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
                $qtyForCheck = $this->quoteItemQtyList
                    ->getQty($productId, $quoteItemId, $quoteId, 0);
            }
            /**
             * we are using 0 because original qty was processed
             */

        } else {
            $increaseQty = $quoteItem->getQtyToAdd() ?: $qty;
            $rowQty = $qty;
            $qtyForCheck = $this->quoteItemQtyList->getQty(
                $productId,
                $quoteItemId,
                $quoteId,
                $increaseQty
            );
        }

        $productTypeCustomOption = $product->getCustomOption('product_type');
        if ($productTypeCustomOption !== null) {
            // Check if product related to current item is a part of product that represents product set
            if ($this->typeConfig->isProductSet($productTypeCustomOption->getValue())) {
                $stockItem->setIsChildItem(true);
            }
        }

        $stockItem->setProductName($product->getName());

        /** @var DataObject $result */
        $result = $this->stockState->checkQuoteItemQty(
            $productId,
            $rowQty,
            $qtyForCheck,
            $qty,
            $product->getStore()->getWebsiteId()
        );

        /* We need to ensure that any possible plugin will not erase the data */
        $backOrdersQty = $this->stockStateProvider->checkQuoteItemQty($stockItem, $rowQty, $qtyForCheck, $qty)
            ->getItemBackorders();
        $result->setItemBackorders($backOrdersQty);

        if ($stockItem->hasIsChildItem()) {
            $stockItem->unsIsChildItem();
        }

        if ($result->getItemIsQtyDecimal() !== null) {
            $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
            }
        }

        /**
         * Just base (parent) item qty can be changed
         * qty of child products are declared just during add process
         * exception for updating also managed by product type
         */
        if ($result->getHasQtyOptionUpdate() && (!$quoteItem->getParentItem() ||
                $quoteItem->getParentItem()->getProduct()->getTypeInstance()->getForceChildItemQtyChanges(
                    $quoteItem->getParentItem()->getProduct()
                )
            )
        ) {
            $quoteItem->setData('qty', $result->getOrigQty());
        }

        if ($result->getItemUseOldQty() !== null) {
            $quoteItem->setUseOldQty($result->getItemUseOldQty());
        }

        if ($result->getMessage() !== null) {
            $quoteItem->setMessage($result->getMessage());
        }

        if ($result->getItemBackorders() !== null) {
            $quoteItem->setBackorders($result->getItemBackorders());
        }

        $quoteItem->setStockStateResult($result);

        return $result;
    }
}


