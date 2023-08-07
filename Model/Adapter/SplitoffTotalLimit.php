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

/**
 * Class SplitoffTotalLimit
 * @package Splitoff\TogetherPay\Model\Adapter
 */
class SplitoffTotalLimit
{
    /**
     * @var Call
     */
    protected $splitoffApiCall;
    protected $splitoffConfig;
    protected $objectManagerInterface;
    protected $jsonHelper;

    /**
     * SplitoffTotalLimit constructor.
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
     * @return mixed|\Zend_Http_Response
     */
    public function getLimit($override = [])
    {
        $queryString=array("include"=>"cbt");
        /** @var \Splitoff\TogetherPay\Model\Config\Payovertime $url */
        $url = $this->splitoffConfig->getApiUrl('v2/configuration',$queryString); //V2

        // calling API
        try {
            $response = $this->splitoffApiCall->send($url, null, null, $override);
        }
        catch (\Exception $e) {

            $state =  $this->objectManagerInterface->get('Magento\Framework\App\State');
            if ($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
                throw new \Exception($e->getMessage());
            }
            else {
                $response = $this->objectManagerInterface->create('Splitoff\TogetherPay\Model\Payovertime');
                $response->setBody($this->jsonHelper->jsonEncode([
                    'error' => 1,
                    'message' => $e->getMessage()
                ]));
            }
        }

        return $response;
    }
}
