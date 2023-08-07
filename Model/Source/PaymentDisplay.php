<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Source;

class PaymentDisplay implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Option to set redirect or lightbox
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'redirect',
                'label' => 'Redirect',
            ],
            [
                'value' => 'lightbox',
                'label' => 'Lightbox',
            ]

        ];
    }
}
