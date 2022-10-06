<?php

namespace MasMag\OrderFrom\Plugin;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderRepositoryPlugin
{

    /**
     * @var Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    public function __construct(
        Session $authSession
    )
    {
        $this->authSession = $authSession;
    }

    public function afterSave(OrderRepositoryInterface $orderRepo, $order)
    {
        $user = $this->authSession->getUser();
        $objectManager = ObjectManager::getInstance();
        $resource = $objectManager->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $orderId = $order->getId();
        if ($user) {
            $checkout_from = 'backend =>' . $user->getEmail();
        } else {
            $checkout_from = 'frontend';
        }
        $query_update = /** @lang text */
            "UPDATE `sales_order` SET `order_from`='$checkout_from' WHERE `entity_id`='$orderId'";
        $connection->query($query_update);
        return $order;
    }
}
