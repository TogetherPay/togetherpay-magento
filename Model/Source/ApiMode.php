<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Source;

/**
 * Class ApiMode
 * @package Splitoff\TogetherPay\Model\Source
 */
class ApiMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * protected object manager
     */
    protected $objectManager;
    //protected $helper;
    protected $logger;

    /**
     * ApiMode constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Psr\Log\LoggerInterface $logger )
    {
        $this->objectManager = $objectManager;
        //$this->helper = $helper;
        $this->logger = $logger;
    //    $e = new \Exception();
   //        $this->logger->debug('VN: api mode source'  . $e->getTraceAsString());
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        // get api mode model to get from XML
        $apiMode = $this->objectManager->create('Splitoff\TogetherPay\Model\Adapter\ApiMode');

        //debug VN
      //  $e = new \Exception();
      //  $this->logger->debug('VN: Api Mode Source.' . $e->getTraceAsString());
        //$this->helper->debug('VN1 Splitoff\TogetherPay\Model\Adapter\ApiMode');

        // looping all data from api modes
        foreach ($apiMode->getAllApiModes() as $name => $environment) {
            array_push(
                $result,
                [
                    'value' => $name,
                    'label' => $environment['label'],
                ]
            );
        }

        // get the result
        return $result;
    }
}
