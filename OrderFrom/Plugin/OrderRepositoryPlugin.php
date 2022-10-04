<?php

namespace Ebranditalia\OrderFrom\Plugin;

use Ebranditalia\Base\Logger\Logger;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\Model\Auth\Session;

class OrderRepositoryPlugin {

    /**
     * @var Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    public function __construct(
        Logger $logger,
        Session $authSession
    )
    {
        $this->logger = $logger;
        $this->authSession = $authSession;
    }

    public function afterSave(OrderRepositoryInterface $orderRepo, $order)
    {
        try {
            $user = $this->authSession->getUser();
            $this->logger->debug("****** OBSERVER EXECUTE ******");
            $objectManager = ObjectManager::getInstance();
            $resource = $objectManager->get(ResourceConnection::class);
            $connection = $resource->getConnection();
            $orderId = $order->getId();
            $this->logger->debug("******  ESEGUO QUERY ORDINE: ".$orderId." ******");
            if ($user) {
                $checkout_from = 'backend =>'.$user->getEmail();
            } else {
                $checkout_from = 'frontend';
            }
            $query_update = /** @lang text */
                "UPDATE `sales_order` SET `order_from`='$checkout_from' WHERE `entity_id`='$orderId'";
            $connection->query($query_update );
            $this->logger->debug("******  HO SALVATO L'ORDINE: " . $checkout_from . " ******");
        } catch (\Exception $e) {
            $this->logger->debug("****** EXCEPTION ORDER FROM " . $checkout_from . " ******");
        }
        return $order;
    }
}