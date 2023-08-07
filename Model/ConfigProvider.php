<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class ConfigProvider
 * @package Splitoff\TogetherPay\Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    const TERMS_CONDITION_LINK = "https://togetherpay.io/terms/";
    /**
     * @var Config\Payovertime
     */
    protected $splitoffConfig;

    /**
     * ConfigProvider constructor.
     * @param Config\Payovertime $config
     */
    public function __construct(\Splitoff\TogetherPay\Model\Config\Payovertime $config)
    {
        $this->splitoffConfig = $config;
    }

    /**
     * Get config set on JS global variable window.checkoutConfig
     *
     * @return array
     */
    public function getConfig()
    {
        // set default array
        $config = [];

        /**
         * adding config array
         */
        $config = array_merge_recursive($config, [
            'payment' => [
                'splitoff' => [
                    'splitoffJs'        => $this->splitoffConfig->getWebUrl('splitoff.js'),
                    'splitoffReturnUrl' => 'splitoff/payment/response',
                    'paymentAction'     => $this->splitoffConfig->getPaymentAction(),
                    'termsConditionUrl' => self::TERMS_CONDITION_LINK,
                    'currencyCode'     => $this->splitoffConfig->getCurrencyCode(),
                ],
            ],
        ]);

        return $config;
    }
}
