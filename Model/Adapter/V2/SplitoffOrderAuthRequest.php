<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Adapter\V2;

use \Splitoff\TogetherPay\Model\Adapter\Splitoff\Call;
use \Splitoff\TogetherPay\Model\Config\Payovertime as PayovertimeConfig;
use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;
use \Magento\Store\Model\StoreManagerInterface as StoreManagerInterface;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use \Splitoff\TogetherPay\Helper\Data as Helper;

/**
 * Class SplitoffOrderAuthRequest
 * @package Splitoff\TogetherPay\Model\Adapter\V2
 */
class SplitoffOrderAuthRequest
{
    protected $splitoffApiCall;
    protected $splitoffConfig;
    protected $objectManagerInterface;
    protected $storeManagerInterface;
    protected $jsonHelper;
    protected $helper;

    /**
     * SplitoffOrderAuthRequest constructor.
     * @param Call $splitoffApiCall
     * @param PayovertimeConfig $splitoffConfig
     * @param ObjectManagerInterface $objectManagerInterface
     * @param toreManagerInterface $storeManagerInterface
     * @param JsonHelper $jsonHelper
     * @param Helper $splitoffHelper
     */
    public function __construct(
        Call $splitoffApiCall,
        PayovertimeConfig $splitoffConfig,
        ObjectManagerInterface $objectManagerInterface,
        StoreManagerInterface $storeManagerInterface,
        JsonHelper $jsonHelper,
        Helper $splitoffHelper
    ) {
        $this->splitoffApiCall = $splitoffApiCall;
        $this->splitoffConfig = $splitoffConfig;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $splitoffHelper;
    }

    /**
     * @param $token
     * @param $merchant_order_id
     * @return mixed|\Zend_Http_Response
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate($token, $merchant_order_id)
    {
        $requestData = $this->_buildAuthRequest($token, $merchant_order_id);

        try {
            $response = $this->splitoffApiCall->send(
                $this->splitoffConfig->getApiUrl('v2/payments/auth'),
                $requestData,
                \Laminas\Http\Request::METHOD_POST
            );
        } catch (\Exception $e) {
            $response = $this->objectManagerInterface->create('Splitoff\TogetherPay\Model\Payovertime');
            $response->setBody($this->jsonHelper->jsonEncode([
                'error' => 1,
                'message' => $e->getMessage()
            ]));
        }

        return $response;
    }
	/**
     * @param $token
     * @param $merchant_order_id
     * @return array
     */
    protected function _buildAuthRequest($token, $merchant_order_id)
    {
        $params['requestId'] = uniqid();
        $params['merchantReference'] = $merchant_order_id;
        $params['token'] = $token;

        return $params;
    }
}
