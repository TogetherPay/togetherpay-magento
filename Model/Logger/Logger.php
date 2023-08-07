<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Logger;

class Logger extends \Monolog\Logger
{

  public function __construct($name, array $handlers = array(), array $processors = array()) {

    if (is_null($name)) {
      $name = "TogetherPay";
    }

    parent::__construct($name, $handlers, $processors);
  }

}
