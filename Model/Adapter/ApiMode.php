<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Adapter;
//use Splitoff\TogetherPay\Helper\Data as SplitoffHelper;

class ApiMode
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $environments;
    //protected $helper;
    protected $logger;


    /**
     * Mode constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $environments
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Psr\Log\LoggerInterface $logger, $environments = [])
    {
        $this->scopeConfig = $scopeConfig;
        $this->environments = $environments;
        //$this->helper = $helper;
        $this->logger = $logger;
  //      $e = new \Exception();
  //      $this->logger->debug('VN: api mode adapter'  . $e->getTraceAsString());
    }

    /**
     * Get All API modes from di.xml
     *
     * @return array
     */
    public function getAllApiModes()
    {
      //debug VN
    //  $e = new \Exception();
    //  $this->logger->debug('VN: getAllApiModes' . $e->getTraceAsString());
      //$this->helper->debug('VN1 Splitoff\TogetherPay\Model\Adapter\ApiMode');

        return $this->environments;
    }

    /**
     * Get current API mode based on configuration
     *
     * @return array
     */
    public function getCurrentMode($override = [])
    {
      //debug VN
      //$this->logger->debug('VN1 ');
      //$this->helper->debug('VN1 Splitoff\TogetherPay\Model\Adapter\ApiMode');

        if (!empty($override["website_id"])) {
            return $this->environments[$this->scopeConfig->getValue('payment/splitoffpayovertime/api_mode', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES, $override["website_id"])];
        }
        return $this->environments[$this->scopeConfig->getValue('payment/splitoffpayovertime/api_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)];
    }
}
