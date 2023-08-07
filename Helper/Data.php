<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_logger;
    protected $_splitoffConfig;
    protected $_moduleList;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Splitoff\TogetherPay\Model\Logger\Logger $logger,
        \Splitoff\TogetherPay\Model\Config\Payovertime $splitoffConfig,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_splitoffConfig = $splitoffConfig;
        $this->_moduleList = $moduleList;
    }

    public function debug($message, array $context = [])
    {
        if ($this->_splitoffConfig->isDebugEnabled()) {
            return $this->_logger->debug($message, $context);
        }
    }

    public function getModuleVersion()
    {
        $moduleInfo = $this->_moduleList->getOne('Splitoff_TogetherPay');
        return $moduleInfo['setup_version'];
    }

	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
