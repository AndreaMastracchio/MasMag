<?php

/**
 *
 * Class PromoMessage
 * @package MasMag\PromoMessage\Helper
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */

namespace MasMag\PromoMessage\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

class PromoMessage extends AbstractHelper
{
    public float $quoteValue;
    public bool $isEnabled;
    public Session $checkoutSession;

    public function __construct(
        Context              $context,
        ScopeConfigInterface $scopeConfig,
        Session              $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->isEnabled = (bool)$scopeConfig->getValue('promo_message/general/is_enabled', ScopeInterface::SCOPE_STORE);
        $this->quoteValue = (float)$scopeConfig->getValue('promo_message/promo_config/quote_minimal', ScopeInterface::SCOPE_STORE);
        parent::__construct($context);
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getFreeMessage(): string
    {
        $quote_totals = $this->checkoutSession->getQuote()->getSubtotal();
        $config_quote = $this->getConfigPromoQuote();
        if ($quote_totals > $config_quote) {
            return '';
        }
        return __('You are %1 euro short of free shipping!', ($config_quote - $quote_totals));
    }

    /**
     * @return float
     */
    public function getConfigPromoQuote(): float
    {
        return $this->quoteValue;
    }

    /**
     * @return bool
     */
    public function messageIsEnabled(): bool
    {
        return $this->isEnabled;
    }
}
