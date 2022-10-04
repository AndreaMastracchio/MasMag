<?php

/**
 *
 * Class ProductFree
 * @package MasMag\FreeProduct\Helper
 *
 * @author Andrea Gregorio Mastracchio
 * @email <andreamastracchio@live.com>
 *
 */

namespace MasMag\FreeProduct\Helper;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use MasMag\FreeProduct\Utility\ProductFree as UtilityProductFree;

class ProductFree extends AbstractHelper
{

    public const group_partner = 2;
    public const group_not_logged = 0;

    /**
     * @var Cart
     */
    private Cart $cart;
    /**
     * @var UtilityProductFree
     */
    private UtilityProductFree $productFree;

    public function __construct(
        Context            $context,
        Cart               $cart,
        UtilityProductFree $productFree
    )
    {
        $this->cart = $cart;
        $this->productFree = $productFree;
        parent::__construct($context);
    }

    public function checkIfFreeProductIsPresent(): bool
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item->getSku() === $this->productFree->partnerProductFree ||
                $item->getSku() === $this->productFree->noPartnerProductFree
            ) {
                return true;
            }
        }
        return false;
    }

    public function isProductFree($product): bool
    {
        return $product->getSku() === $this->productFree->partnerProductFree || $product->getSku() === $this->productFree->noPartnerProductFree;
    }
}
