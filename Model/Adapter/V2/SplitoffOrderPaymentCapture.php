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
 * Class SplitoffOrderPaymentCapture
 * @package Splitoff\TogetherPay\Model\Adapter\V2
 */
class SplitoffOrderPaymentCapture
{
    protected $splitoffApiCall;
    protected $splitoffConfig;
    protected $objectManagerInterface;
    protected $storeManagerInterface;
    protected $jsonHelper;
    protected $helper;

    /**
     * SplitoffOrderPaymentCapture constructor.
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
     * @param $totalAmount
     * @param $merchant_order_id
     * @param array $splitoff_order_id
     * @return mixed|\Zend_Http_Response
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send($totalAmount,$merchant_order_id,$splitoff_order_id,$override=[])
    {
        $requestData = $this->_buildPaymentCaptureRequest($totalAmount, $merchant_order_id);

        try {
            $response = $this->splitoffApiCall->send(
                //$this->splitoffConfig->getApiUrl('v2/payments/'.$splitoff_order_id.'/capture',[],$override),
                $this->splitoffConfig->getApiUrl('refund/id='.$splitoff_order_id,[],$override),
                $requestData,
                \Laminas\Http\Request::METHOD_POST,
				$override
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
     * @param $totalAmount
     * @param $merchant_order_id
     * @return array
     */
    protected function _buildPaymentCaptureRequest($totalAmount, $merchant_order_id)
    {
		$params['requestId'] = uniqid();
        $params['merchantReference'] = $merchant_order_id;
        $params['amount'] = $totalAmount;

        return $params;
    }
}
