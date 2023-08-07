<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\GuestPaymentInformationManagement;

class Plugin
{
    /**
     * @var \Splitoff\TogetherPay\Model\Token
     */
    protected $token;

    /**
     * Plugin constructor.
     * @param \Splitoff\TogetherPay\Model\Token $token
     */
    public function __construct(
        \Splitoff\TogetherPay\Model\Token $token
    ) {
        $this->token = $token;
    }

    /**
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @param $returnValue
     * @return string
     */
    public function afterSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $returnValue
    ) {
        return $this->token->saveAndReturnToken($returnValue);
    }
}
