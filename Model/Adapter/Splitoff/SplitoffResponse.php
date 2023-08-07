<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Adapter\Splitoff;

/**
 * Class SplitoffResponse
 * @package Splitoff\TogetherPay\Model\Adapter\Splitoff
 * @see \Zend\Http\Response
 */
class SplitoffResponse
{
	/**
     * The Response Body
     */
	private $body;

	/**
     * The Response Status
     */
	private $status;

	/**
     * Get Response Status
     *
     * @return string
     */
	public function getStatus()
    {
    	return $this->status;
    }

    /**
     * Set Response Status
     *
     * @param string $status 	HTTP Status
     */
	public function setStatus($status)
    {
    	$this->status = $status;
    }

	/**
     * Get Response Body
     *
     * @return string
     */
	public function getBody()
    {
    	return $this->body;
    }

    /**
     * Set Response Body
     *
     * @param string $body 	HTTP Body
     */
	public function setBody($body)
    {
    	$this->body = $body;
    }
}
