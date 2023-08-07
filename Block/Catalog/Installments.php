<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Block\Catalog;

use Magento\Framework\Registry as Registry;
use Splitoff\TogetherPay\Model\Config\Payovertime as SplitoffConfig;
use Splitoff\TogetherPay\Model\Payovertime as SplitoffPayovertime;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Locale\Resolver as Resolver;

class Installments extends \Splitoff\TogetherPay\Block\JsConfig
{

    protected $registry;
    protected $splitoffConfig;
    protected $splitoffPayovertime;
    private $localeResolver;

    /**
     * Installments constructor.
     * @param Context $context
     * @param SplitoffConfig $splitoffConfig
     * @param SplitoffPayovertime $splitoffPayovertime
     * @param Registry $registry
     * @param SplitoffConfig $splitoffConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SplitoffConfig $splitoffConfig,
        SplitoffPayovertime $splitoffPayovertime,
        array $data,
        Resolver $localeResolver
    ) {
        $this->registry = $registry;
        $this->splitoffConfig = $splitoffConfig;
        $this->splitoffPayovertime = $splitoffPayovertime;
        $this->localeResolver = $localeResolver;
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
			if($this->splitoffConfig->getCurrencyCode()){
				if($this->splitoffPayovertime->canUseForCurrency($this->splitoffConfig->getCurrencyCode())){
					$excluded_categories=$this->splitoffConfig->getExcludedCategories();
					if($excluded_categories!=""){
						$excluded_categories_array =  explode(",",$excluded_categories);
						$product = $this->registry->registry('product');
						$categoryids = $product->getCategoryIds();
						foreach($categoryids as $k)
						{
							if(in_array($k,$excluded_categories_array)){
								return false;
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
    public function getTypeOfProduct()
    {
        $product = $this->registry->registry('product');
		return $product->getTypeId();
    }

    /**
     * @return string
     */
    public function getFinalAmount()
    {
        // get product
        $product = $this->registry->registry('product');

        // set if final price is exist
        $price = $product->getFinalPrice();

        return !empty($price)?number_format($price, 2,".",""):"0.00";

    }
    /**
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
