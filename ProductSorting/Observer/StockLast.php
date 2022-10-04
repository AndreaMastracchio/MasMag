<?php

/**
 *
 * Class StockLast
 * @package MasMag\ProductSorting\Observer
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */

namespace MasMag\ProductSorting\Observer;

use Magento\Catalog\Block\Product\ProductList\Toolbar as CoreToolbar;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class StockLast implements ObserverInterface
{
    protected ScopeConfigInterface $scopeConfig;
    protected StoreManagerInterface $_storeManager;
    protected CoreToolbar $coreToolbar;

    public function __construct(
        ScopeConfigInterface  $scopeConfig,
        StoreManagerInterface $storeManager,
        CoreToolbar           $toolbar
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->coreToolbar = $toolbar;
    }

    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getData('collection');
        $websiteId = 0;
        $collection->getSelect()->joinLeft(
            array('_inv' => $collection->getResource()->getTable('cataloginventory_stock_status')),
            "_inv.product_id = e.entity_id and _inv.website_id=$websiteId",
            array('stock_status')
        );
        $collection->addExpressionAttributeToSelect('in_stock', 'IFNULL(_inv.stock_status,0)', array());
        $collection->getSelect()->reset('order');
        $collection->getSelect()->order('in_stock DESC');
        return $this;
    }
}
