<?php

/**
 *
 * Class ValidateCartObserver
 * @package MasMag\FreeProduct\Helper
 *
 * @author Andrea Gregorio Mastracchio
 * @email <andreamastracchio@live.com>
 *
 */

namespace MasMag\FreeProduct\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use MasMag\FreeProduct\Helper\ProductFree as Helper;
use MasMag\FreeProduct\Utility\ProductFree;

class ValidateCartObserver implements ObserverInterface
{

    /**
     * @var RedirectInterface
     */
    protected RedirectInterface $redirect;

    /**
     * @var Cart
     */
    protected Cart $cart;

    /**
     * @var ProductFree
     */
    private ProductFree $productUtility;
    private Helper $helper;

    /**
     *
     * @param Cart $cart
     * @param ProductFree $productUtility
     * @param Helper $helper
     */
    public function __construct(
        Cart        $cart,
        ProductFree $productUtility,
        Helper      $helper
    )
    {
        $this->cart = $cart;
        $this->productUtility = $productUtility;
    }

    /**
     *
     * Validate Cart Before going to checkout
     * event: controller_action_predispatch sul cart e il checkout
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->productUtility->isEnabled) {
            $quote_subtotal = $this->cart->getQuote()->getSubtotal();
            $items = $this->cart->getQuote()->getAllItems();
            $customer_group = $this->productUtility->getCustomerGroup();
            $product_free_in_cart = $this->productUtility->checkFreeProductNotPresent($items, $customer_group);
            $product_free_was_purchase = $this->productUtility->checkIfFreeProductWasPurchased();
            if (!$product_free_in_cart && !$product_free_was_purchase) {
                if ($customer_group === Helper::group_partner &&
                    $quote_subtotal >= $this->productUtility->partnerMinPurchase
                ) {
                    $this->productUtility->addFreeSamplerProduct(
                        $this->productUtility->getProductFree($this->productUtility->partnerProductFree)
                    );
                } else if ($customer_group !== Helper::group_not_logged &&
                    $quote_subtotal >= $this->productUtility->noPartnerMinPurchase
                ) {
                    $this->productUtility->addFreeSamplerProduct(
                        $this->productUtility->getProductFree($this->productUtility->noPartnerProductFree)
                    );
                }
            } else if ($customer_group === Helper::group_partner &&
                $quote_subtotal <= $this->productUtility->partnerMinPurchase &&
                $product_free_in_cart
            ) {
                $this->productUtility->removeFreeSamplerProduct(
                    $this->productUtility->getProductFree($this->productUtility->partnerProductFree),
                    $items
                );
            } else if (($customer_group !== Helper::group_not_logged && $customer_group !== Helper::group_partner) &&
                $quote_subtotal <= $this->productUtility->noPartnerMinPurchase &&
                $product_free_in_cart
            ) {
                $this->productUtility->removeFreeSamplerProduct(
                    $this->productUtility->getProductFree($this->productUtility->noPartnerProductFree),
                    $items
                );
            }
        }
    }
}
