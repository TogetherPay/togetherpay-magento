<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Block;

/**
 * Class Info
 * @package Splitoff\TogetherPay\Block
 */
class Info extends \Magento\Payment\Block\Info
{
    /**
     * @param null $transport
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $info = $this->getInfo();

        // load the data available on additional informations
        if ($this->_appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
            && $info->getAdditionalInformation()
        ) {
            foreach ($info->getAdditionalInformation() as $field => $value) {
                $beautifiedFieldName = str_replace('_', ' ', ucwords(trim(preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $field))));
                $data[__($beautifiedFieldName)->getText()] = $value;
            }
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }
}
