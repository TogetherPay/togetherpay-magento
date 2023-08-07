<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model;

class Token
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     * @var \Magento\Checkout\Model\Session
     */
    protected $jsonHelper;
    protected $checkoutSession;

    /**
     * Token constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param $return
     * @return string
     */
    public function saveAndReturnToken($return)
    {
        // checking if splitoff payment is being use
        $order = $this->checkoutSession->getLastRealOrder();
        $payment = $order->getPayment();

        if ($payment->getMethod() == \Splitoff\TogetherPay\Model\Payovertime::METHOD_CODE) {
            $data = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ADDITIONAL_INFORMATION_KEY_TOKEN);
            $return = $this->jsonHelper->jsonEncode([
                'token' => $data
            ]);
        }

        return $return;
    }
}
