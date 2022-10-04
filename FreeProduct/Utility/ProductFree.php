<?php

/**
 *
 * Class ProductFree
 * @package MasMag\FreeProduct\Utility
 *
 * @author Andrea Gregorio Mastracchio
 * @email <andreamastracchio@live.com>
 *
 */

namespace MasMag\FreeProduct\Utility;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use MasMag\FreeProduct\Helper\ProductFree as Helper;

class ProductFree
{
    /**
     * @var Session
     */
    private Session $customerSession;
    /**
     * @var Cart
     */
    private Cart $cart;
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var Order
     */
    private Order $order;
    /**
     * @var mixed
     */
    public $partnerProductFree;
    public $noPartnerProductFree;
    public $partnerMinPurchase;
    public $noPartnerMinPurchase;
    public $isEnabled;

    public function __construct(
        Session              $customerSession,
        ScopeConfigInterface $scopeConfig,
        Cart                 $cart,
        ProductRepository    $productRepository,
        Order                $order
    )
    {
        $this->customerSession = $customerSession;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->partnerProductFree = $scopeConfig->getValue('free_product/free_product_partner/sampler_partner', ScopeInterface::SCOPE_STORE);
        $this->partnerMinPurchase = $scopeConfig->getValue('free_product/free_product_partner/sampler_min_partner', ScopeInterface::SCOPE_STORE);
        $this->noPartnerProductFree = $scopeConfig->getValue('free_product/free_product_no_partner/sampler_no_partner', ScopeInterface::SCOPE_STORE);
        $this->noPartnerMinPurchase = $scopeConfig->getValue('free_product/free_product_no_partner/sampler_min_no_partner', ScopeInterface::SCOPE_STORE);
        $this->isEnabled = $scopeConfig->getValue('free_product/general/sampler_enabled', ScopeInterface::SCOPE_STORE);
        $this->order = $order;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function addFreeProductToCart($product): void
    {
        $quote_subtotal = $this->cart->getQuote()->getSubtotal();
        $items = $this->cart->getQuote()->getAllItems();
        $customer_group = $this->getCustomerGroup();
        if ($product->getSku() !== $this->partnerProductFree &&
            $product->getSku() !== $this->noPartnerProductFree
        ) {
            if (($customer_group === Helper::group_partner && $quote_subtotal >= $this->partnerMinPurchase) &&
                $this->checkFreeProductNotPresent($items, $customer_group) &&
                !$this->checkIfFreeProductWasPurchased()
            ) {
                $this->addFreeSamplerProduct($this->getProductFree($this->partnerProductFree));
            } else if ((($customer_group !== Helper::group_partner && $customer_group !== Helper::group_not_logged)
                    && $quote_subtotal >= $this->noPartnerMinPurchase) &&
                $this->checkFreeProductNotPresent($items, $customer_group) &&
                !$this->checkIfFreeProductWasPurchased()
            ) {
                $this->addFreeSamplerProduct($this->getProductFree($this->noPartnerProductFree));
            }
        }
    }


    public function getCustomerGroup(): int
    {
        return $this->customerSession->getCustomer()->getGroupId();
    }

    /**
     * @throws LocalizedException
     */
    public function addFreeSamplerProduct($product_free): void
    {
        $params = array(
            'product_add' => $product_free->getSku(),
        );
        $this->cart->addProduct($product_free, $params);
        $this->cart->save();
    }

    public function removeFreeSamplerProduct($product_free, $items): void
    {
        foreach ($items as $item) {
            if ($item->getSku() === $product_free->getSku()) {
                $this->cart->removeItem($item->getId());
            }
        }
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getProductFree($sku): Product
    {
        return $this->productRepository->get($sku);
    }

    public function checkFreeProductNotPresent($items, $customer_group): bool
    {
        foreach ($items as $item) {
            if ($customer_group === Helper::group_partner && $item->getSku() === $this->partnerProductFree) {
                return true;
            }

            if ($item->getSku() === $this->noPartnerProductFree) {
                return true;
            }
        }
        return false;
    }

    public function checkIfFreeProductWasPurchased(): bool
    {
        $customer_id = $this->customerSession->getCustomer()->getId();
        $customer_group = $this->getCustomerGroup();
        $orders = $this->order->getCollection()->addFieldToFilter('customer_id', $customer_id);
        foreach ($orders as $order) {
            foreach ($order->getAllItems() as $item) {
                if ($customer_group === Helper::group_partner && $item->getSku() === $this->partnerProductFree) {
                    return true;
                }

                if (($customer_group !== Helper::group_not_logged && $customer_group !== Helper::group_partner) && $item->getSku() === $this->noPartnerProductFree) {
                    return true;
                }
            }
        }
        return false;
    }
}
