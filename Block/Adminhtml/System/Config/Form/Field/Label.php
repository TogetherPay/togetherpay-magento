<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use \Splitoff\TogetherPay\Helper\Data as SplitoffHelper;

class Label extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $helper;

    /**
     * Call constructor.
     * @param SplitoffHelper $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        SplitoffHelper $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }


    protected function _getElementHtml(AbstractElement $element)
    {
        $version = $this->helper->getModuleVersion();
        return $version;
    }
}
