<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Adapter;

use \Splitoff\TogetherPay\Model\Adapter\Splitoff\Call;
use \Splitoff\TogetherPay\Model\Config\Payovertime as SplitoffConfig;
use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;
use \Magento\Framework\Json\Helper\Data as JsonHelper;

class SplitoffPayment
{
    /**
     * constant variable
     */
    const API_RESPONSE_APPROVED = 'APPROVED';

    /**
     * @var Call
     */
    protected $splitoffApiCall;
    protected $splitoffConfig;
    protected $objectManagerInterface;
    protected $jsonHelper;

    /**
     * SplitoffPayment constructor.
     * @param Call $splitoffApiCall
     * @param SplitoffConfig $splitoffConfig
     * @param ObjectManagerInterface $objectManagerInterface
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Call $splitoffApiCall,
        SplitoffConfig $splitoffConfig,
        ObjectManagerInterface $objectManagerInterface,
        JsonHelper $jsonHelper
    ) {
        $this->splitoffApiCall = $splitoffApiCall;
        $this->splitoffConfig = $splitoffConfig;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @param $splitoffOrderId
     * @return mixed|\Zend_Http_Response
     */
    public function getPayment($splitoffOrderId, $override = [])
    {
        return $this->_getPayment($splitoffOrderId, false, $override);
    }

    /**
     * @param $token
     * @return mixed|\Zend_Http_Response
     */
    public function getPaymentByToken($token, $override = [])
    {
        return $this->_getPayment($token, true, $override);
    }

    /**
     * @param $input
     * @param bool $useToken
     * @return mixed|\Zend_Http_Response
     */
    protected function _getPayment($input, $useToken = false, $override = [])
    {
        // set url for ID
        $url = $this->splitoffConfig->getApiUrl('merchants/orders/' . $input, [], $override);

        // if request using token create url for it
        if ($useToken) {
            $url = $this->splitoffConfig->getApiUrl('merchants/orders/', ['token' => $input], $override);
        }

        try {
            $response = $this->splitoffApiCall->send($url, null, null, $override);
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
     * @param $amount
     * @param $orderId
     * @param string $currency
     * @param array $override
     * @return mixed|\Zend_Http_Response
     */
    public function refund($amount, $orderId, $currency = 'AUD', $override = [])
    {
        // create url to request refunds
        //$url = $this->splitoffConfig->getApiUrl('v2/payments/' . $orderId . '/refund', [], $override);
        $url = $this->splitoffConfig->getApiUrl('refund?id=' . $orderId, [], $override);

        // generate body to be sent to refunds
        $body = [
		    'requestId'  => uniqid(),
            'amount'    => [
                'amount'    => (string)abs(round($amount, 2)), // Splitoff API V2 requires a positive amount
                'currency'  => $currency,
            ],
            'merchantReference'  => $orderId
        ];


        // refunding now
        try {
            $response = $this->splitoffApiCall->send(
                $url,
                $body,
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
     * @param $orderId
     * @param array $override
     * @return mixed|\Zend_Http_Response
     */
    public function voidOrder($orderId, $override = [])
    {
        // create url to request refunds
        $url = $this->splitoffConfig->getApiUrl('v2/payments/' . $orderId . '/void', [], $override);

        // refunding now
        try {
            $response = $this->splitoffApiCall->send(
                $url,
                "",
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
}
