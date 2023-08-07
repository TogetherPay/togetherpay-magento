<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Splitoff\TogetherPay\Helper\Data as Helper;

/**
 * Class BeforeCreditmemoLoad
 * @package Splitoff\TogetherPay\Observer
 */
class BeforeCreditmemoLoad implements ObserverInterface
{
  protected $_helper;
  protected $_layout;
  protected $_registry;

  public function __construct(
	Helper $helper,
	\Magento\Framework\View\LayoutInterface $layout,
	\Magento\Framework\Registry $registry
  )
  {
    $this->_helper = $helper;
	$this->_layout = $layout;
	$this->_registry = $registry;
  }

 public function execute(\Magento\Framework\Event\Observer $observer)
  {
	$block = $observer->getEvent()->getBlock();
	$layout = $block->getLayout();

	if($layout->hasElement('sales_creditmemo_create')){
		$creditmemo = $this->_registry->registry('current_creditmemo');
		if($creditmemo){
			$order      = $creditmemo->getOrder();
			$payment    = $order->getPayment();

			if($payment->getMethod() == \Splitoff\TogetherPay\Model\Payovertime::METHOD_CODE ){
				$splitoffPaymentStatus = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::PAYMENT_STATUS);
				if($splitoffPaymentStatus == \Splitoff\TogetherPay\Model\Response::PAYMENT_STATUS_AUTH_APPROVED || $splitoffPaymentStatus == \Splitoff\TogetherPay\Model\Response::PAYMENT_STATUS_PARTIALLY_CAPTURED){
					$block->unsetChild(
						'submit_offline'
					);
					if($layout->hasElement('customerbalance.creditmemo')){
						$layout->unsetElement('customerbalance.creditmemo');
					}
				}
			}
		}
	}
  }
}
?>
