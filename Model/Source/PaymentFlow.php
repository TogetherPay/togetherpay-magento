<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Source;

/**
 * Class PaymentFlow
 * @package Splitoff\TogetherPay\Model\Source
 */
class PaymentFlow implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * PaymentFlow constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
			['value' => 'immediate', 'label' => __('Immediate Payment Flow')],			
		];
    }
}
