<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */
namespace WeltPixel\LayeredNavigation\Block\Navigation;

/**
 * Layered navigation state
 *
 * @api
 * @since 100.0.2
 */
class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    /**
     * @var string
     */
    protected $_template = 'WeltPixel_LayeredNavigation::layer/state.phtml';

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_wpHelper = $wpHelper;
        parent::__construct($context,$layerResolver, $data);
    }

}
