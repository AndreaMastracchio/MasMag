<?php

namespace MasMag\Bundle\Model\Quote\Item;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Helper\Data;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;

class QuantityValidator extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator
{
    /**
     * @var QuantityValidator\Initializer\Option
     */
    protected $optionInitializer;

    /**
     * @var QuantityValidator\Initializer\StockItem
     */
    protected $stockItemInitializer;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    public function __construct(
        Option                 $optionInitializer,
        StockItem              $stockItemInitializer,
        StockRegistryInterface $stockRegistry,
        StockStateInterface    $stockState,
        ProductRepository      $productRepository
    )
    {
        $this->productRepository = $productRepository;
        parent::__construct($optionInitializer, $stockItemInitializer, $stockRegistry, $stockState);
    }

    /**
     * Check product inventory data when quote item quantity declaring
     *
     * @param Observer $observer
     *
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function validate(Observer $observer)
    {
        /* @var $quoteItem Item */
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem ||
            !$quoteItem->getProductId() ||
            !$quoteItem->getQuote()
        ) {
            return;
        }
        $product = $quoteItem->getProduct();
        $qty = $quoteItem->getQty();

        /* @var Stock\Item $stockItem */
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        if (!$stockItem instanceof StockItemInterface) {
            throw new LocalizedException(__('The Product stock item is invalid. Verify the stock item and try again.'));
        }

        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            foreach ($options as $option) {
                $this->optionInitializer->initialize($option, $quoteItem, $qty);
            }
        } else {
            $this->stockItemInitializer->initialize($stockItem, $quoteItem, $qty);
        }

        if ($quoteItem->getQuote()->getIsSuperMode()) {
            return;
        }

        /* @var StockStatusInterface $stockStatus */
        $stockStatus = $this->stockRegistry->getStockStatus($product->getId(), $product->getStore()->getWebsiteId());

        /* @var StockStatusInterface $parentStockStatus */
        $parentStockStatus = false;

        /**
         * Check if product in stock. For composite products check base (parent) item stock status
         */
        if ($quoteItem->getParentItem()) {
            $product = $quoteItem->getParentItem()->getProduct();
            $parentStockStatus = $this->stockRegistry->getStockStatus(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
        }

        if ($stockStatus) {
            if ($stockStatus->getStockStatus() === Stock::STOCK_OUT_OF_STOCK
                || $parentStockStatus && $parentStockStatus->getStockStatus() == Stock::STOCK_OUT_OF_STOCK
            ) {
                $quoteItem->addErrorInfo(
                    'cataloginventory',
                    Data::ERROR_QTY,
                    __('This product is out of stock.')
                );
                $quoteItem->getQuote()->addErrorInfo(
                    'stock',
                    'cataloginventory',
                    Data::ERROR_QTY,
                    __('Some of the products are out of stock.')
                );
                return;
            } else {
                // Delete error from item and its quote, if it was set due to item out of stock
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Data::ERROR_QTY);
            }
        }

        /**
         * Check item for options
         */
        if ($options) {
            $qty = $product->getTypeInstance()->prepareQuoteItemQty($qty, $product);
            $quoteItem->setData('qty', $qty);
            if ($stockStatus) {
                $this->checkOptionsQtyIncrements($quoteItem, $options);
            }

            // variable to keep track if we have previously encountered an error in one of the options
            $removeError = true;
            foreach ($options as $option) {
                $result = $option->getStockStateResult();
                if ($result->getHasError()) {
                    $option->setHasError(true);
                    //Setting this to false, so no error statuses are cleared
                    $removeError = false;
                    $this->addErrorInfoToQuote($result, $quoteItem, $removeError);
                }
            }
            if ($removeError) {
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Data::ERROR_QTY);
            }
        } else {
            if ($quoteItem->getParentItem() === null) {
                $result = $quoteItem->getStockStateResult();
                if ($result->getHasError()) {
                    $this->addErrorInfoToQuote($result, $quoteItem);
                } else {
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, Data::ERROR_QTY);
                }
            }
        }
    }

    /**
     * Removes error statuses from quote and item, set by this observer
     *
     * @param Item $item
     * @param int $code
     * @return void
     */
    protected function _removeErrorsFromQuoteAndItem($item, $code)
    {
        if ($item->getHasError()) {
            $params = ['origin' => 'cataloginventory', 'code' => $code];
            $item->removeErrorInfosByParams($params);
        }

        $quote = $item->getQuote();
        if ($quote->getHasError()) {
            $quoteItems = $quote->getItemsCollection();
            $canRemoveErrorFromQuote = true;
            foreach ($quoteItems as $quoteItem) {
                if ($quoteItem->getItemId() == $item->getItemId()) {
                    continue;
                }

                $errorInfos = $quoteItem->getErrorInfos();
                foreach ($errorInfos as $errorInfo) {
                    if ($errorInfo['code'] == $code) {
                        $canRemoveErrorFromQuote = false;
                        break;
                    }
                }

                if (!$canRemoveErrorFromQuote) {
                    break;
                }
            }

            if ($canRemoveErrorFromQuote) {
                $params = ['origin' => 'cataloginventory', 'code' => $code];
                $quote->removeErrorInfosByParams(null, $params);
            }
        }
    }

    /**
     * Verifies product options quantity increments.
     *
     * @param Item $quoteItem
     * @param array $options
     * @return void
     * @throws NoSuchEntityException
     */
    private function checkOptionsQtyIncrements(Item $quoteItem, array $options): void
    {
        $removeErrors = true;
        foreach ($options as $option) {
            $product = $this->productRepository->getById($option->getProductId());
            $result = $this->stockState->checkQtyIncrements(
                $option->getProduct()->getId(),
                $this->stockRegistry->getStockItem($product->getId())->getMinSaleQty(),
                $option->getProduct()->getStore()->getWebsiteId()
            );
            if ($result->getHasError()) {
                $quoteItem->getQuote()->addErrorInfo(
                    $result->getQuoteMessageIndex(),
                    'cataloginventory',
                    Data::ERROR_QTY_INCREMENTS,
                    $result->getQuoteMessage()
                );

                $removeErrors = false;
            }
        }

        if ($removeErrors) {
            // Delete error from item and its quote, if it was set due to qty problems
            $this->_removeErrorsFromQuoteAndItem(
                $quoteItem,
                Data::ERROR_QTY_INCREMENTS
            );
        }
    }

    /**
     * Add error information to Quote Item
     *
     * @param DataObject $result
     * @param Item $quoteItem
     * @return void
     */
    private function addErrorInfoToQuote($result, $quoteItem)
    {
        $quoteItem->addErrorInfo(
            'cataloginventory',
            Data::ERROR_QTY,
            $result->getMessage()
        );

        $quoteItem->getQuote()->addErrorInfo(
            $result->getQuoteMessageIndex(),
            'cataloginventory',
            Data::ERROR_QTY,
            $result->getQuoteMessage()
        );
    }
}
