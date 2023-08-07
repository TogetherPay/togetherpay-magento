<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Block\Cart;

use Magento\Checkout\Model\Session as CheckoutSession;
use Splitoff\TogetherPay\Model\Config\Payovertime as SplitoffConfig;
use Splitoff\TogetherPay\Model\Payovertime as SplitoffPayovertime;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Locale\Resolver as Resolver;


class Button extends \Splitoff\TogetherPay\Block\JsConfig
{
    /**
     * @var SplitoffConfig
     */
    protected $splitoffConfig;
    protected $splitoffPayovertime;
    protected $checkoutSession;
    protected $customerSession;

    /**
     * Button constructor.
     * @param Context $context
     * @param SplitoffConfig $splitoffConfig
     * @param SplitoffPayovertime $splitoffPayovertime
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param array $data
     * @param Resolver $localeResolver
     */
    public function __construct(
        Context $context,
        SplitoffConfig $splitoffConfig,
        SplitoffPayovertime $splitoffPayovertime,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        Resolver $localeResolver,
        array $data=[]
    ) {
        $this->splitoffConfig = $splitoffConfig;
        $this->splitoffPayovertime = $splitoffPayovertime;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        parent::__construct($splitoffConfig,$context, $localeResolver,$data);
    }

    /**
     * @return bool
     */
    protected function _getPaymentIsActive()
    {
        return $this->splitoffConfig->isActive();
    }

    /**
     * @return bool
     */
    public function canShow()
    {
		 // check if payment is active
        if (!$this->_getPaymentIsActive()) {
            return false;
        }
		else{
			//Check for Supported currency
			if($this->splitoffConfig->getCurrencyCode()){

				$quote = $this->checkoutSession->getQuote();
				// get grand total (final amount need to be paid)
				$grandTotal =$quote->getGrandTotal();
				$excluded_categories=$this->splitoffConfig->getExcludedCategories();

				if($this->splitoffPayovertime->canUseForCurrency($this->splitoffConfig->getCurrencyCode()) ){

					if($excluded_categories !=""){
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
						$productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
						$excluded_categories_array =  explode(",",$excluded_categories);

						foreach ($quote->getAllVisibleItems() as $item) {
							$productid = $item->getProductId();
							$product=$productRepository->getById($productid);
							$categoryids = $product->getCategoryIds();

							foreach($categoryids as $k)
							{
								if(in_array($k,$excluded_categories_array)){
									return false;
								}
							}
						}
					}
					return true;
				}
				else{
					return false;
				}
			}
			else {
				return false;
			}
		}
    }

    /**
     * @return string
     */
    public function getFinalAmount()
    {

        $grandTotal = $this->checkoutSession->getQuote()->getGrandTotal();

        return !empty($grandTotal)?number_format($grandTotal, 2,".",""):"0.00";

    }
    /*
     * @return boolean
    */
    public function canUseCurrency()
    {
        $canUse=false;
        //Check for Supported currency
        if($this->splitoffConfig->getCurrencyCode())
        {
            $canUse= $this->splitoffPayovertime->canUseForCurrency($this->splitoffConfig->getCurrencyCode());
        }

        return $canUse;

    }
}
