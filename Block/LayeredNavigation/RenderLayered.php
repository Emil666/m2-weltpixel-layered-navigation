<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */
namespace WeltPixel\LayeredNavigation\Block\LayeredNavigation;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Framework\View\Element\Template;

/**
 * Class RenderLayered
 * @package WeltPixel\LayeredNavigation\Block\LayeredNavigation
 */
class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{

    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'WeltPixel_LayeredNavigation::product/layered/renderer.phtml';

    /**
     * @var \WeltPixel\LayeredNavigation\Model\AttributeOptions
     */
    protected $_attributeOptions;

    /**
     * RenderLayered constructor.
     * @param Template\Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param \WeltPixel\LayeredNavigation\Model\AttributeOptions $attributeOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \WeltPixel\LayeredNavigation\Model\AttributeOptions $attributeOptions,
        array $data = []
    ) {
        $this->eavAttribute = $eavAttribute;
        $this->layerAttribute = $layerAttribute;
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->_attributeOptions = $attributeOptions;

        parent::__construct($context,$eavAttribute,$layerAttribute,$swatchHelper,$mediaHelper, $data);
    }

    /**
     * return the 'Is Multiselect' attribute configuration value
     *
     * @return mixed
     */
    public function getIsMultiSelect($attrId)
    {
        return ($attrId > 0) ? $this->getWpAttributeOptions($attrId)->getIsMultiselect() : 0;
    }

    /**
     * Return wp attribute options
     *
     * @param $attributeId
     * @return mixed
     */
    public function getWpAttributeOptions($attrId)
    {
        $this->_attributeOptionsObj = ($attrId > 0) ? $this->_attributeOptions->getDisplayOptionsByAttribute($attrId) : '';

        return $this->_attributeOptionsObj;
    }


}
