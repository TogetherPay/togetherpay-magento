<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Model\Adapter\Splitoff;

use Splitoff\TogetherPay\Helper\Data as SplitoffHelper;
use Splitoff\TogetherPay\Model\Config\Payovertime as SplitoffConfig;
use Laminas\Http\Client;
use Laminas\Http\Request;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Call
 * @package Splitoff\TogetherPay\Model\Adapter\Splitoff
 */
class Call
{
    /**
     * @var for HTTP Client
     */
    protected $client;
    protected $jsonHelper;
    protected $helper;

    /**
     * Call constructor.
     * @param ZendClientFactory $httpClientFactory
     * @param SplitoffConfig $splitoffConfig
     * @param JsonHelper $jsonHelper
     * @param SplitoffHelper $helper
     */
    public function __construct(
        Client $httpClient,
        SplitoffConfig $splitoffConfig,
        JsonHelper $jsonHelper,
        SplitoffHelper $helper
    ) {
        /** HTTP Client and splitoff config */
        $this->httpClient = $httpClient;
        $this->splitoffConfig = $splitoffConfig;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
    }

    /**
     * Send using HTTP call
     * The HTTP Support can switched between the Zend Client and Splitoff Client
     * This is to provide Fallback to the Zend Client related issues
     *
     * @param $url
     * @param bool $body
     * @param string $method
     * @param array $override
     * @return \Splitoff\TogetherPay\Model\Adapter\Splitoff\SplitoffResponse
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send($url, $body = false, $method = \Laminas\Http\Request::METHOD_GET, $override = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        // set the client http
        if ($this->splitoffConfig->isHTTPHeaderSupportEnabled()) {
            $client = $objectManager->get('Splitoff\TogetherPay\Model\Adapter\Splitoff\SplitoffClient');
        } else {
            $client = $objectManager->get('Splitoff\TogetherPay\Model\Adapter\Splitoff\SplitoffClient');
        }

        $client->setUri($url);

        // set body and the url
        if ($body || ($method == \Laminas\Http\Request::METHOD_POST || $method == \Laminas\Http\Request::METHOD_PUT)) {
            $newbody='';
            if (!empty($body)) {
                $newbody=$this->jsonHelper->jsonEncode($body);
            }

           $client->setRawData($newbody, 'application/json');
        }

        // add auth for API requirements
        $client->setAuth(
            trim($this->splitoffConfig->getMerchantId($override)),
            trim($this->splitoffConfig->getMerchantKey($override))
        );

        //$client->setHeader('Origin', '*');
        //Additional debugging on the merchant ID and Key being sent on Update Payment Limits
        $queryString=["include"=>"cbt"];

        /*
        if ($url == $this->splitoffConfig->getApiUrl('v2/configuration', $queryString) ||
            $url == $this->splitoffConfig->getApiUrl('merchants/valid-payment-types')) {
            //Solves the problem of magento 2 cron not working for some merchants
            if (array_key_exists('REQUEST_URI', $_SERVER)) {
                $this->helper->debug('Merchant Origin: ' . $_SERVER['REQUEST_URI']);
            }
            $this->helper->debug('Target URL: ' . $url);
            $this->helper->debug('Merchant ID:' . $this->splitoffConfig->getMerchantId($override));

            $merchant_key = $this->splitoffConfig->getMerchantKey($override);

            $masked_merchant_key = substr($merchant_key, 0, 4) . '****' . substr($merchant_key, -4);

            $this->helper->debug('Merchant Key:' . $masked_merchant_key);
        }
*/
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion(); //will return the magento version
        $description = $productMetadata->getName() . ' ' . $productMetadata->getEdition(); //will return the magento description

        /*
        if (!empty($override['website_id'])) {
            $url = $this->getWebsiteUrl($override['website_id']);
        } else {
            $url = $this->getWebsiteUrl();
        }
        */

        // set configurations
        $client->setConfig(
            [
                'timeout'           => 80,
                'maxredirects'      => 0,
                'useragent'         => 'SplitoffMagento2Plugin ' . $this->helper->getModuleVersion() . ' (' . $description . ' ' . $version . ')' . ' PHPVersion: PHP/' . phpversion() . ' MerchantID: ' . trim($this->splitoffConfig->getMerchantId($override) . ' URL: ' . $url)
            ]
        );

        // debug mode
        $requestLog = [
            'type' => 'Request',
            'method' => $method,
            'url' => $url,
            'body' => $this->obfuscateCustomerData($body)
        ];
        $this->helper->debug($this->jsonHelper->jsonEncode($requestLog));

        // do the request with catch
        try {
            $response = $client->request($method);
            $responseBody = $response->getBody();

            try {
                $responseBody = $this->jsonHelper->jsonDecode($responseBody);
            } catch (\Exception $e) {
                $this->helper->debug("A non JSON response was received. Cf-ray ID : " . $responseBody);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }

            // debug mode
            $responseLog = [
                'type' => 'Response',
                'method' => $method,
                'url' => $url,
                'httpStatusCode' => $response->getStatus(),
                'body' => $this->obfuscateCustomerData($responseBody)
            ];
            $this->helper->debug($this->jsonHelper->jsonEncode($responseLog));
        } catch (\Exception $e) {
            $this->helper->debug($e->getMessage());

            throw new \Magento\Framework\Exception\LocalizedException(
                __('Gateway error: %1', $e->getMessage())
            );
        }

        // return response
        return $response;
    }

    private function getWebsiteUrl($website_id = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $url = null;

        if (!empty($website_id)) {
            $websites = $storeManager->getWebsites();

            foreach ($websites as $website) {
                foreach ($website->getStores() as $store) {
                    if (!empty($website_id) && $website_id == $website->getId()) {
                        $storeObj = $storeManager->getStore($store);
                        $url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                    }
                }
            }
        } else {
            $url = $storeManager->getStore()->getBaseUrl();
        }

        return $url;
    }

    private function obfuscateCustomerData($body = [])
    {
        $fieldsToObfuscate= ["shipping","billing","consumer",'orderDetails'];
        $body_replace=[];
        if (!empty($body) && is_array($body)) {
            foreach ($body as $body_key=>$body_value) {
                if (in_array($body_key, $fieldsToObfuscate)) {
                    $body_replace[$body_key]=[];

                    foreach ($body_value as $key=>$value) {
                        if (is_array($value)) {
                            $body_replace[$body_key] = $this->obfuscateCustomerData($body_value);
                        } else {
                            if ($value) {
                                $body_replace[$body_key][$key]=str_repeat("*", strlen($value));
                            } else {
                                $body_replace[$body_key][$key]='';
                            }
                        }
                    }
                }
            }
            return array_replace($body, $body_replace);
        }
        return $body;
    }
}
