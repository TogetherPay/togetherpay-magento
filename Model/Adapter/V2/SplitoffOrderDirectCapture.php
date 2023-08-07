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
 * Class SplitoffOrderDirectCapture
 * @package Splitoff\TogetherPay\Model\Adapter\V2
 */
class SplitoffOrderDirectCapture
{
    protected $splitoffApiCall;
    protected $splitoffConfig;
    protected $objectManagerInterface;
    protected $storeManagerInterface;
    protected $jsonHelper;
    protected $helper;

    /**
     * SplitoffOrderDirectCapture constructor.
     * @param Call $splitoffApiCall
     * @param PayovertimeConfig $splitoffConfig
     * @param ObjectManagerInterface $objectManagerInterface
     * @param StoreManagerInterface $storeManagerInterface
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
        $requestData = $this->_buildDirectCaptureRequest($token, $merchant_order_id);

        try {
            $response = $this->splitoffApiCall->send(
                //$this->splitoffConfig->getApiUrl('v2/payments/capture'),
                $this->splitoffConfig->getApiUrl('capture_payment'),
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
    protected function _buildDirectCaptureRequest($token, $merchant_order_id)
    {
        $params['merchantReference'] = $merchant_order_id;
        $params['token'] = $token;

        return $params;
    }
}
