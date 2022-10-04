<?php

namespace MasMag\Bundle\Model;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as ObjectFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Math\Division as MathDivision;

class StockStateProvider extends \MasMag\Catalog\Model\StockStateProvider
{
    public function __construct(MathDivision $mathDivision, FormatInterface $localeFormat, ObjectFactory $objectFactory, ProductFactory $productFactory, $qtyCheckApplicable = true)
    {
        parent::__construct($mathDivision, $localeFormat, $objectFactory, $productFactory, $qtyCheckApplicable);
    }

    /**
     * Check Qty Increments
     *
     * @param StockItemInterface $stockItem
     * @param float|int $qty
     * @return DataObject
     */
    public function checkQtyIncrements(StockItemInterface $stockItem, $qty): DataObject
    {
        $result = new DataObject();
        if ($stockItem->getSuppressCheckQtyIncrements()) {
            return $result;
        }

        $qtyIncrements = $stockItem->getQtyIncrements() * 1;

        if ($stockItem->getIsChildItem()) {
            $result->setHasError(false);
        } else if ($qtyIncrements && $this->mathDivision->getExactDivision($qty, $qtyIncrements) !== 0) {
            $result->setHasError(true)
                ->setQuoteMessage(__('Please correct the quantity for some products.'))
                ->setErrorCode('qty_increments')
                ->setQuoteMessageIndex('qty');
            if ($stockItem->getIsChildItem()) {
                $result->setMessage(
                    __(
                        'You can buy %1 only in quantities of %2 at a time.',
                        $stockItem->getProductName(),
                        $qtyIncrements
                    )
                );
            } else {
                $result->setMessage(__('You can buy this product only in quantities of %1 at a time.', $qtyIncrements));
            }
        }
        return $result;
    }
}
