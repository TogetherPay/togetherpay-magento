<?php
/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
namespace Splitoff\TogetherPay\Controller\Payment;

use \Magento\Payment\Model\Method\AbstractMethod;
use \Magento\Store\Model\ScopeInterface;

/**
 * Class Response
 * @package Splitoff\TogetherPay\Controller\Payment
 */
class Response extends \Magento\Framework\App\Action\Action
{
    const DEFAULT_REDIRECT_PAGE = 'checkout/cart';

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;
    protected $response;
    protected $_helper;
    protected $_checkoutSession;
    protected $_jsonHelper;
    protected $_splitoffConfig;
    protected $_directCapture;
    protected $_authRequest;
    protected $_tokenCheck;
    protected $_quoteManagement;
    protected $_transactionBuilder;
    protected $_orderSender;
    protected $_orderRepository;
    protected $_paymentRepository;
    protected $_transactionRepository;
    protected $_notifierPool;
    protected $_paymentCapture;
    protected $_quoteValidator;
    protected $_timezone;
    protected $_splitoffApiPayment;
    protected $quotePaymentCollectionFactory;
    protected $cartRepository;
    protected $magentoResponse;
    protected $scopeConfig;
    /**
     * @var \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderDirectCancel
     */
    private $_directCancel;

    /**
     * Response constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Splitoff\TogetherPay\Model\Response $response
     * @param \Splitoff\TogetherPay\Helper\Data $helper
     * @param \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderDirectCapture $directCapture
     * @param \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderDirectCancel $directCancel
     * @param \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderAuthRequest $authRequest
     * @param \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderTokenCheck $tokenCheck
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Splitoff\TogetherPay\Model\Config\Payovertime $splitoffConfig
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\Order\Payment\Repository $paymentRepository
     * @param \Magento\Sales\Model\Order\Payment\Transaction\Repository $transactionRepository
     * @param \Magento\Framework\Notification\NotifierInterface $notifierPool
     * @param \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderPaymentCapture $paymentCapture
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Splitoff\TogetherPay\Model\Adapter\SplitoffPayment $splitoffApiPayment
     * @param \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Framework\App\ResponseInterface $magentoResponse
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Splitoff\TogetherPay\Model\Response $response,
        \Splitoff\TogetherPay\Helper\Data $helper,
        \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderDirectCapture $directCapture,
        \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderDirectCancel $directCancel,
        \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderAuthRequest $authRequest,
        \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderTokenCheck $tokenCheck,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Splitoff\TogetherPay\Model\Config\Payovertime $splitoffConfig,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\Payment\Repository $paymentRepository,
        \Magento\Sales\Model\Order\Payment\Transaction\Repository $transactionRepository,
        \Magento\Framework\Notification\NotifierInterface $notifierPool,
        \Splitoff\TogetherPay\Model\Adapter\V2\SplitoffOrderPaymentCapture $paymentCapture,
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Splitoff\TogetherPay\Model\Adapter\SplitoffPayment $splitoffApiPayment,
        \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\App\ResponseInterface $magentoResponse,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->response = $response;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_jsonHelper = $jsonHelper;
        $this->_directCapture = $directCapture;
        $this->_directCancel = $directCancel;
        $this->_authRequest = $authRequest;
        $this->_tokenCheck = $tokenCheck;
        $this->_splitoffConfig = $splitoffConfig;
        $this->_quoteManagement = $quoteManagement;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_orderSender = $orderSender;
        $this->_orderRepository = $orderRepository;
        $this->_paymentRepository = $paymentRepository;
        $this->_transactionRepository = $transactionRepository;
        $this->_notifierPool = $notifierPool;
        $this->_paymentCapture = $paymentCapture;
        $this->_quoteValidator = $quoteValidator;
        $this->_timezone = $timezone;
        $this->_splitoffApiPayment = $splitoffApiPayment;
        $this->quotePaymentCollectionFactory = $quotePaymentCollectionFactory;
        $this->cartRepository = $cartRepository;
        $this->magentoResponse = $magentoResponse;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * Actual action when accessing url
     */
    public function execute()
    {
        $badParams = $this->getRequest()->getParams();
        foreach ($badParams as $key => $value) {
            if (mb_substr($key, 0, 1) == '&') {
                $redirect = 'splitoff/payment/response/';

                $params = explode('&', $key);
                foreach ($params as $param) {
                    if ($param) {
                        $par[] = explode('=', $param);
                    }
                }
                unset($params);
                foreach ($par as $elem) {
                    if (isset($elem[0])) {
                        $params[$elem[0]] = $elem[1];
                    }
                }

                $this->_redirect($redirect, $params);
                return $redirect;
            }
        }
        // debug mode
        $this->_helper->debug('Start \Splitoff\TogetherPay\Controller\Payment\Response::execute() with request ' . $this->_jsonHelper->jsonEncode($this->getRequest()->getParams()));

        $query = $this->getRequest()->getParams();
        $order = $this->_checkoutSession->getLastRealOrder();

        // Check if not fraud detected not doing anything (let cron update the order if payment successful)
        if ($this->_splitoffConfig->getPaymentAction() == AbstractMethod::ACTION_AUTHORIZE_CAPTURE) {
            //Steven - Bypass the response and do capture
            $redirect = $this->_processAuthCapture($query);
        } elseif (!$this->response->validCallback($order, $query)) {
            $this->_helper->debug('Request redirect url is not valid.');
        }
        // debug mode
        $this->_helper->debug('Finished \Splitoff\TogetherPay\Controller\Payment\Response::execute()');

        $this->magentoResponse->setHeader('Location', $redirect);

        // Redirect to cart
        $this->_redirect($redirect);
    }

    private function _processAuthCapture($query)
    {
        $redirect = self::DEFAULT_REDIRECT_PAGE;
        try {
            switch ($query['status']) {
                case \Splitoff\TogetherPay\Model\Response::RESPONSE_STATUS_CANCELLED:
                    if (!isset($query['orderToken'])) {
                        $this->messageManager->addError("Token not provided.");
                        throw new \Exception('Token not provided');
                    }

                    $quotePaymentCollection = $this->quotePaymentCollectionFactory->create();
                    $quotePaymentCollection
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('method', ['eq' => 'splitoffpayovertime'])
                        ->addFieldToFilter('additional_information', ['eq' => '{"splitoff_token":"' . $query['orderToken'] . '"}']);
                    $quotePayment = $quotePaymentCollection->getFirstItem();
                    if (!$quotePayment) {
                        $this->messageManager->addError("Quote not found.");
                        throw new \Exception('Quote not found');
                    }
                    $quote = $this->cartRepository->get($quotePayment->getQuoteId());
                    $this->_checkoutSession->replaceQuote($quote);

                    $this->messageManager->addError(__('You have cancelled your Splitoff payment. Please select an alternative payment method.'));
                    break;
                case \Splitoff\TogetherPay\Model\Response::RESPONSE_STATUS_FAILURE:
                    $this->messageManager->addError(__('Splitoff payment failure. Please select an alternative payment method.'));
                    break;
                case \Splitoff\TogetherPay\Model\Response::RESPONSE_STATUS_SUCCESS:
                    //Steven - Capture here

                    if (!isset($query['orderToken'])) {
                        $this->messageManager->addError("Token not provided.");
                        throw new \Exception('Token not provided');
                    }

                    $quotePaymentCollection = $this->quotePaymentCollectionFactory->create();
                    $quotePaymentCollection
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('method', ['eq' => 'splitoffpayovertime'])
                        ->addFieldToFilter('additional_information', ['eq' => '{"splitoff_token":"' . $query['orderToken'] . '"}']);
                    $quotePayment = $quotePaymentCollection->getFirstItem();
                    if (!$quotePayment) {
                        $this->messageManager->addError("Quote not found.");
                        throw new \Exception('Quote not found');
                    }
                    $quote = $this->cartRepository->get($quotePayment->getQuoteId());
                    $quote->setIsActive(true);
                    $this->cartRepository->save($quote);
                    $this->_checkoutSession->replaceQuote($quote);

                    //$quote = $this->_checkoutSession->getQuote();
                    $payment = $quote->getPayment();
                    $this->_quoteValidator->validateBeforeSubmit($quote);

                    $token = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ADDITIONAL_INFORMATION_KEY_TOKEN);
                    $merchant_order_id = $quote->getReservedOrderId();

                    if (!$this->_helper->getConfig('payment/splitoffpayovertime/payment_flow') || $this->_helper->getConfig('payment/splitoffpayovertime/payment_flow') == "immediate" || $quote->getIsVirtual()) {

                        $this->_helper->debug("Starting Payment Capture request.");

                        // CALL CAPTURE_PAYMENT API URL
                        $response = $this->_directCapture->generate($token, $merchant_order_id);
                    } else {

                        $this->_helper->debug("Starting Auth request.");
                        $response = $this->_authRequest->generate($token, $merchant_order_id);
                    }

                    $response = $this->_jsonHelper->jsonDecode($response->getBody());
                    if (empty($response['status'])) {
                        $response['status'] = \Splitoff\TogetherPay\Model\Response::RESPONSE_STATUS_DECLINED;
                        $this->_helper->debug("Transaction Exception (Empty Response): " . json_encode($response));
                    }
                    switch ($response['status']) {
                        case \Splitoff\TogetherPay\Model\Response::RESPONSE_STATUS_APPROVED:
                            $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ADDITIONAL_INFORMATION_KEY_ORDERID, $response['id']);

                            $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::PAYMENT_STATUS, $response['paymentState']);

                            if ($response['status'] == \Splitoff\TogetherPay\Model\Response::PAYMENT_STATUS_AUTH_APPROVED && array_key_exists('events', $response)) {
                                try {
                                    $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::AUTH_EXPIRY, $this->_timezone->date($response['events'][0]['expires'])->format('Y-m-d H:i T'));
                                } catch (\Exception $e) {
                                    $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::AUTH_EXPIRY, $this->_timezone->date($response['events'][0]['expires'], null, false)->format('Y-m-d H:i T'));
                                    $this->_helper->debug($e->getMessage());
                                }
                            }

                            $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::OPEN_TOCAPTURE_AMOUNT, array_key_exists('openToCaptureAmount', $response) && !empty($response['openToCaptureAmount']) ? $response['openToCaptureAmount']['amount'] : "0.00");

                            $this->_checkoutSession
                                ->setLastQuoteId($quote->getId())
                                ->setLastSuccessQuoteId($quote->getId())
                                ->clearHelperData();

                            //Store Customer email address in temporary variable
                            $customerEmailAddress = $quote->getCustomerEmail();

                            // Create Order From Quote

                            $quote->collectTotals();

                            // Restore Customer email address if it becomes null/blank
                            if (empty($quote->getCustomerEmail())) {
                                $quote->setCustomerEmail($customerEmailAddress);
                            }
                            //Catch the deadlock exception while creating the order and retry 3 times

                            $tries = 0;
                            $lastErrorMessage = "";
                            do {
                                $retry = false;

                                try {
                                    $this->_helper->debug("Trying Order Creation. Try number:" . $tries);
                                    $order = $this->_quoteManagement->submit($quote);
                                } catch (\Exception $e) {
                                    $lastErrorMessage = $e->getMessage();

                                    if (preg_match('/SQLSTATE\[40001\]: Serialization failure: 1213 Deadlock found/', $e->getMessage()) && $tries < 2) {
                                        $this->_helper->debug("Waiting for a second before retrying the Order Creation");
                                        $retry = true;
                                        sleep(1);
                                    } else {
                                        //Reverse or void the order
                                        $orderId = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ADDITIONAL_INFORMATION_KEY_ORDERID);
                                        $paymentStatus = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::PAYMENT_STATUS);

                                        if ($paymentStatus == \Splitoff\TogetherPay\Model\Response::PAYMENT_STATUS_AUTH_APPROVED) {
                                            $voidResponse = $this->_splitoffApiPayment->voidOrder($orderId);
                                            $voidResponse = $this->_jsonHelper->jsonDecode($voidResponse->getBody());

                                            if (!array_key_exists("errorCode", $voidResponse)) {
                                                $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::PAYMENT_STATUS, $voidResponse['paymentState']);

                                                if (array_key_exists('openToCaptureAmount', $voidResponse) && !empty($voidResponse['openToCaptureAmount'])) {
                                                    $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::OPEN_TOCAPTURE_AMOUNT, $voidResponse['openToCaptureAmount']['amount']);
                                                }

                                                $this->_helper->debug('Order Exception : There was a problem with order creation. Splitoff Order ' . $orderId . ' Voided.' . $e->getMessage());
                                                $this->_directCancel->cancel($token, $merchant_order_id, 'Order Exception : There was a problem with order creation. Splitoff Order ' . $orderId . " Voided.\r\n" . $e->getMessage());
                                                throw new \Magento\Framework\Exception\LocalizedException(__('There was a problem placing your order. Your Splitoff order ' . $orderId . ' is refunded.'));
                                            } else {
                                                $this->_helper->debug("Transaction Exception : " . json_encode($voidResponse));
                                                $this->_notifierPool->addMajor(
                                                    'Splitoff Order Failed',
                                                    'There was a problem with an Splitoff order. Order number : ' . $response['id'] . ' and the merchant order number : ' . $merchant_order_id,
                                                    ''
                                                );

                                                $this->_directCancel->cancel($token, $merchant_order_id,
                                                    "There was a problem with an Splitoff order. Order number : " . $response['id'] . " and the merchant order number : " . $merchant_order_id .
                                                    "\r\n Transaction Exception : " . json_encode($voidResponse) . "\r\n " . $e->getMessage());
                                                throw new \Magento\Framework\Exception\LocalizedException(__('There was a problem placing your order.'));
                                            }
                                        } else {
                                            $orderTotal = $quote->getGrandTotal();

                                            $refundResponse = $this->_splitoffApiPayment->refund(number_format($orderTotal, 2, '.', ''), $orderId, $quote->getQuoteCurrencyCode());

                                            $refundResponse = $this->_jsonHelper->jsonDecode($refundResponse->getBody());

                                            if (!empty($refundResponse['refundId'])) {
                                                $this->_helper->debug('Order Exception : There was a problem with order creation. Splitoff Order ' . $orderId . ' refunded.' . $e->getMessage());
                                                $this->_directCancel->cancel($token, $merchant_order_id,'Order Exception : There was a problem with order creation. Splitoff Order ' . $orderId . " refunded.\r\n" . $e->getMessage());
                                                throw new \Magento\Framework\Exception\LocalizedException(__('There was a problem placing your order. Your Splitoff order ' . $orderId . ' is refunded.'));
                                            } else {
                                                $this->_helper->debug("Transaction Exception : " . json_encode($refundResponse));
                                                $this->_directCancel->cancel($token, $merchant_order_id,
                                                    'Splitoff Order Failed' .
                                                    "\r\nThere was a problem with an Splitoff order. Order number : " . $response['id'] . " and the merchant order number : " . $merchant_order_id .
                                                    "\r\nTransaction Exception : " . json_encode($refundResponse) .
                                                    "\r\n" . $e->getMessage());
                                                $this->_notifierPool->addMajor(
                                                    'Splitoff Order Failed',
                                                    'There was a problem with an Splitoff order. Order number : ' . $response['id'] . ' and the merchant order number : ' . $merchant_order_id,
                                                    ''
                                                );
                                                throw new \Magento\Framework\Exception\LocalizedException(__('There was a problem placing your order.'));
                                            }
                                        }
                                    }
                                }
                                $tries++;
                            } while ($tries < 3 && $retry);

                            if ($order) {

                                $payment = $order->getPayment();

                                if ($payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::PAYMENT_STATUS) == \Splitoff\TogetherPay\Model\Response::PAYMENT_STATUS_AUTH_APPROVED) {
                                    $totalDiscount = $this->_calculateTotalDiscount($order);
                                    if ($totalDiscount > 0) {
                                        $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ROLLOVER_DISCOUNT, $this->_calculateTotalDiscount($order));
                                    }
                                    $this->_captureVirtual($order, $payment);
                                }

                                $this->_checkoutSession->setLastOrderId($order->getId())
                                    ->setLastRealOrderId($order->getIncrementId())
                                    ->setLastOrderStatus($order->getStatus());

                                $this->_createTransaction($order, $response, $payment);

                                $this->messageManager->addSuccess("Splitoff Transaction Completed");

                                $redirect = 'checkout/onepage/success';

                            } else {
                                $this->_directCancel->cancel($token, $merchant_order_id, "Order Exception : There was a problem with order creation." .
                                "\r\nTrying Order Creation. Try number: {$tries}" .
                                "\r\n{$lastErrorMessage}");
                                $this->_helper->debug("Order Exception : There was a problem with order creation.");
                            }
                            break;
                        case \Splitoff\TogetherPay\Model\Response::RESPONSE_STATUS_DECLINED:
                            $merchantId = $this->scopeConfig->getValue('payment/splitoffpayovertime/merchant_id', ScopeInterface::SCOPE_STORE);
                            $params = $this->getRequest()->getParams();
                            if (!isset($params['orderToken'])) {
                                $this->messageManager->addError("Order Token not provided.");

                                return $redirect;
                            }

                            $redirect = 'https://dev.splitoff.io/pay/develop/cart.html?orderToken=' . $params['orderToken'] . '&id=' . $merchantId . '&status=declined';
                            //$this->messageManager->addError(__('Splitoff payment declined. Please select an alternative payment method.'));
                            break;
                        default:
                            $this->messageManager->addError($response);
                            break;
                    }
                    break;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_helper->debug("Transaction Exception: " . $e->getMessage());
            $this->messageManager->addError(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->_helper->debug("Transaction Exception: " . $e->getMessage());
            $this->messageManager->addError("There was a problem in placing your order.");
        }

        return $redirect;
    }

    private function _createTransaction($order = null, $paymentData = [], $payment = null)
    {
        try {
            $payment->setLastTransId($paymentData['id']);
            $payment->setTransactionId($paymentData['id']);
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );

            $message = __('The authorized amount is %1.', $formatedPrice);
            //get the object of builder class
            $trans = $this->_transactionBuilder;
            $transaction = $trans->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($paymentData['id'])
                ->setFailSafe(true)
                //build method creates the transaction and returns the object
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId(null);
            $this->_paymentRepository->save($payment);

            $order->setBaseCustomerBalanceInvoiced(null);
            $order->setCustomerBalanceInvoiced(null);
            $this->_orderRepository->save($order);

            $transaction = $this->_transactionRepository->save($transaction);

            return $transaction->getTransactionId();
        } catch (\Exception $e) {
            //log errors here
            $this->_helper->debug("Transaction Exception: There was a problem with creating the transaction. " . $e->getMessage());
        }
    }

    private function _captureVirtual($order = null, $payment = null)
    {
        $totalCaptureAmount = 0.00;

        foreach ($order->getAllItems() as $items) {
            if ($items->getIsVirtual()) {
                $itemPrice = ($items->getQtyOrdered() * $items->getPrice()) + $items->getBaseTaxAmount();
                $totalCaptureAmount = $totalCaptureAmount + ($itemPrice - $items->getDiscountAmount());
            }
        }

        $totalDiscountAmount = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ROLLOVER_DISCOUNT);

        if ($totalDiscountAmount != 0) {
            if ($totalCaptureAmount >= $totalDiscountAmount) {
                $totalCaptureAmount = $totalCaptureAmount - $totalDiscountAmount;
                $totalDiscountAmount = 0.00;
            } else if ($totalCaptureAmount < $totalDiscountAmount) {
                $totalDiscountAmount = $totalDiscountAmount - $totalCaptureAmount;
                $totalCaptureAmount = 0.00;

            }
            $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ROLLOVER_DISCOUNT, number_format($totalDiscountAmount, 2, '.', ''));
        }

        if ($totalCaptureAmount >= 1) {
            $splitoff_order_id = $payment->getAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ADDITIONAL_INFORMATION_KEY_ORDERID);
            $merchant_order_id = $order->getIncrementId();
            $currencyCode = $order->getOrderCurrencyCode();

            $totalAmount = [
                'amount' => number_format($totalCaptureAmount, 2, '.', ''),
                'currency' => $currencyCode
            ];

            $response = $this->_paymentCapture->send($totalAmount, $merchant_order_id, $splitoff_order_id);
            $response = $this->_jsonHelper->jsonDecode($response->getBody());

            if (!array_key_exists("errorCode", $response)) {
                $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::PAYMENT_STATUS, $response['paymentState']);
                if (array_key_exists('openToCaptureAmount', $response) && !empty($response['openToCaptureAmount'])) {
                    $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::OPEN_TOCAPTURE_AMOUNT, $response['openToCaptureAmount']['amount']);
                }
            } else {
                $this->_helper->debug("Transaction Exception : " . json_encode($response));
            }
        } else {
            if ($totalCaptureAmount < 1 && $totalCaptureAmount > 0) {
                $payment->setAdditionalInformation(\Splitoff\TogetherPay\Model\Payovertime::ROLLOVER_AMOUNT, number_format($totalCaptureAmount, 2, '.', ''));
            }
        }

    }

    /*
     Calculate Total Discount for the given order
    */
    private function _calculateTotalDiscount($order)
    {
        $storeCredit = $order->getCustomerBalanceAmount();
        $giftCardAmount = $order->getGiftCardsAmount();
        $totalDiscountAmount = $storeCredit + $giftCardAmount;
        return number_format($totalDiscountAmount, 2, '.', '');
    }
}
