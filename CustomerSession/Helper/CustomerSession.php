<?php

namespace MasMag\CustomerSession\Helper;

use Magento\Customer\Model\Context as ModelContext;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 *
 * Class CustomerSession
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */
class CustomerSession extends AbstractHelper
{
    private HttpContext $httpContext;
    private array $data;
    private Session $customerSession;
    private CustomerFactory $customerFactory;

    /**
     * @param HelperContext $context
     * @param HttpContext $httpContext
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        HelperContext               $context,
        HttpContext                 $httpContext,
        Session                     $customerSession,
        CustomerFactory             $customerFactory
    )
    {
        $this->httpContext = $httpContext;
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }


    public function getCustomerIsLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(ModelContext::CONTEXT_AUTH);
    }

    public function getCustomerEmail(): string
    {
        return $this->httpContext->getValue('customer_email');
    }

    public function getCustomerId(): int
    {
        return $this->httpContext->getValue('customer_id');
    }

    public function getCustomer(): Customer
    {
        $customer_id = $this->httpContext->getValue('customer_id');
        if ($customer_id) {
            return $this->customerFactory->create()->load($customer_id);
        }

        if ($this->customerSession->isLoggedIn()) {
            return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
        }
    }

}
