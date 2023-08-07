<?php

namespace Splitoff\TogetherPay\Block;

use Magento\Framework\View\Element\Template;
use Splitoff\TogetherPay\Model\Config\Payovertime;
use Magento\Framework\Json\Helper\Data;

class Config extends Template
{
    /**
     * @var Payovertime $_payOverTime
     */
    protected $_payOverTime;

    /**
     * @var Data $_dataHelper
     */
    protected $_dataHelper;

    /**
     * Config constructor.
     *
     * @param Payovertime $payovertime
     * @param Data $dataHelper
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Payovertime $payovertime,
        Data $dataHelper,
        Template\Context $context,
        array $data
    ) {

        $this->_payOverTime = $payovertime;
        $this->_dataHelper = $dataHelper;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        return $this;
    }

    /**
     * Get URL to sfterpay.js
     *
     * @return bool|string
     */
    public function getSplitoffJsUrl()
    {
        return $this->_payOverTime->getWebUrl('splitoff.js');
    }
    public function getMerchantId(){
        return $this->_payOverTime->getMerchantId();
    }
    public function getPingUrl(){
        return $this->_payOverTime->getApiUrl('ping');
    }
	/**
     * @return bool
     */
	public function checkCurrency()
    {
		$supportedCurrency=['AUD','NZD','USD','CAD'];
		if(in_array($this->_payOverTime->getCurrencyCode(),$supportedCurrency)){
			return true;
		}
		else{
			return false;
		}
    }
}
