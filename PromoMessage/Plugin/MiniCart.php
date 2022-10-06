<?php

/**
 *
 * Class MiniCart
 * @package MasMag\PromoMessage\Plugin
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */

namespace MasMag\PromoMessage\Plugin;

use Magento\Checkout\CustomerData\Cart;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MasMag\PromoMessage\Helper\PromoMessage;

class MiniCart
{

    private PromoMessage $promoHelper;

    public function __construct(
        PromoMessage $promoHelper
    )
    {
        $this->promoHelper = $promoHelper;
    }

    /**
     * @param Cart $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetSectionData(
        Cart  $subject,
        array $result
    ): array
    {
        if ($this->promoHelper->messageIsEnabled()) {
            $result['message'] = $this->promoHelper->getFreeMessage();
        }
        return $result;
    }
}
