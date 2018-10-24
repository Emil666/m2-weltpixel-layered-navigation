<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RemoveBlocks
 * @package WeltPixel\LayeredNavigation\Observer
 */
class RemoveBlocks implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * RemoveBlocks constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \WeltPixel\LayeredNavigation\Helper\Data $wpHelper
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_wpHelper = $wpHelper;
        $this->_request = $request;
    }

    public function execute(Observer $observer)
    {
        if($this->_isAdvancedSearchResultPage()) {
            return;
        }
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        $wishlistBlock = $layout->getBlock('wishlist_sidebar');
        $compareBlock = $layout->getBlock('catalog.compare.sidebar');


        if($wishlistBlock) {
            $showWishlistBlock = $this->_wpHelper->showWishlistBlock();
            if (!$showWishlistBlock) {
                $layout->unsetElement('wishlist_sidebar');
            }
        }

        if($compareBlock) {
            $showCompareBlock = $this->_wpHelper->showCompareBlock();
            if (!$showCompareBlock ) {
                $layout->unsetElement('catalog.compare.sidebar');
            }
        }
    }

    /**
     * @return bool
     */
    protected function _isAdvancedSearchResultPage() {
        $is = false;
        $controller = $this->_request->getControllerName();
        $action     = $this->_request->getActionName();
        $route      = $this->_request->getRouteName();

        if($route == 'catalogsearch' && $controller == 'advanced' && $action == 'result') {
            $is = true;
        }

        return $is;
    }
}