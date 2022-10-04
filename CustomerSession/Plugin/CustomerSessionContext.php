<?php

namespace MasMag\CustomerSession\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 *
 * Class CustomerSessionContext
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */


class CustomerSessionContext
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var Context
     */
    protected Context $httpContext;

    public function __construct(
        Session     $customerSession,
        Context $httpContext
    )
    {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param ActionInterface $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundDispatch(
        ActionInterface  $subject,
        \Closure         $proceed,
        RequestInterface $request
    )
    {
        $this->httpContext->setValue(
            'customer_id',
            $this->customerSession->getCustomerId(),
            false
        );

        $this->httpContext->setValue(
            'customer_name',
            $this->customerSession->getCustomer()->getName(),
            false
        );

        $this->httpContext->setValue(
            'customer_email',
            $this->customerSession->getCustomer()->getEmail(),
            false
        );

        return $proceed($request);
    }
}
