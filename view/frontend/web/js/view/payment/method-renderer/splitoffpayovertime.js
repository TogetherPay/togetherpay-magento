/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messageList',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/section-config',
		'Magento_Checkout/js/action/set-billing-address',
        'Splitoff_TogetherPay/js/view/payment/method-renderer/splitoffredirect'
    ],
    function ($, Component, quote, resourceUrlManager, storage, mageUrl, additionalValidators, globalMessageList, customerData, sectionConfig,setBillingAddressAction,splitoffRedirect) {
        'use strict';

        return Component.extend({
            /** Don't redirect to the success page immediately after placing order **/
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Splitoff_TogetherPay/payment/splitoffpayovertime',
                billingAgreement: ''
            },

            initialize: function () {
               this._super();

               if( window.Splitoff.merchantId !== ""){
                   $.ajax({ url: window.Splitoff.pingUrl, headers: { 'X-Auth-Token': window.Splitoff.merchantId } });
               }

               return this;
            },

            /**
             * Terms and condition link
             * @returns {*}
             */
            getTermsConditionUrl: function () {
                return window.checkoutConfig.payment.splitoff.termsConditionUrl;
            },

            /**
             * Get Grand Total of the current cart
             * @returns {*}
             */
            getGrandTotal: function () {

                var total = quote.getCalculatedTotal();
                var format = window.checkoutConfig.priceFormat.pattern
				var splitoff = window.checkoutConfig.payment.splitoff;

                storage.get(resourceUrlManager.getUrlForCartTotals(quote), false)
                .done(
                    function (response) {

                        var amount = response.base_grand_total;
                        var installmentFee = response.base_grand_total / 4;
                        var installmentFeeLast = amount - installmentFee.toFixed(window.checkoutConfig.priceFormat.precision) * 3;

                        $(".splitoff_instalments_amount").text(format.replace(/%s/g, installmentFee.toFixed(window.checkoutConfig.priceFormat.precision)));
                        $(".splitoff_instalments_amount_last").text(format.replace(/%s/g, installmentFeeLast.toFixed(window.checkoutConfig.priceFormat.precision)));


						if (splitoff.currencyCode == 'USD' || splitoff.currencyCode == 'CAD' ) {
							 $(".splitoff_total_amount").text(format.replace(/%s/g, installmentFee.toFixed(window.checkoutConfig.priceFormat.precision)));
							return format.replace(/%s/g, installmentFee);
						} else {
							 $(".splitoff_total_amount").text(format.replace(/%s/g, amount.toFixed(window.checkoutConfig.priceFormat.precision)));
							return format.replace(/%s/g, amount);
						}

                    }
                )
                .fail(
                    function (response) {
                       //do your error handling

                    return 'Error';
                    }
                );
            },

            /**
             * Get Checkout Message based on the currency
             * @returns {*}
             */
            getCheckoutText: function () {

                var splitoff = window.checkoutConfig.payment.splitoff;
                var splitoffCheckoutText = '';
                switch(splitoff.currencyCode){
	                case 'USD':
	                	splitoffCheckoutText = 'Checkout';
	                	break;
	                case 'CAD':
	                	splitoffCheckoutText = 'Checkout';
	                	break;
	                default:
	                	splitoffCheckoutText = 'Checkout';
                }

                return splitoffCheckoutText;
            },
			getFirstInstalmentText: function () {

                var splitoff = window.checkoutConfig.payment.splitoff;
                var splitoffFirstInstalmentText = '';

                switch(splitoff.currencyCode){
	                case 'USD':
	                case 'CAD':
	                	splitoffFirstInstalmentText = '';
	                	break;
	                default:
	                	splitoffFirstInstalmentText = '';

                }


                return splitoffFirstInstalmentText;
            },
			getTermsText: function () {

                var splitoff = window.checkoutConfig.payment.splitoff;
                var splitoffTermsText = '';

                switch(splitoff.currencyCode){
	                case 'USD':
	                case 'CAD':
	                	splitoffTermsText = 'You will be redirected to the TogetherPay website to fill out your payment information. You will be redirected back to our site to complete your order.';
	                	break;
	                default:
	                	splitoffTermsText = 'You will be redirected to the TogetherPay website when you proceed to checkout.';
                }

                return splitoffTermsText;
            },
			getTermsLink: function () {

                var splitoff = window.checkoutConfig.payment.splitoff;
                var splitoffCheckoutTermsLink = '';
                switch(splitoff.currencyCode){
	                case 'USD':
	                	splitoffCheckoutTermsLink="https://togetherpay.io/purchase-payment-agreement";
						break;
	                case 'CAD':
						splitoffCheckoutTermsLink="https://togetherpay.io/en-CA/instalment-agreement";
						break;
	                default:
						splitoffCheckoutTermsLink="https://togetherpay.io/terms/";
				}

                return splitoffCheckoutTermsLink;
            },

            /**
             * Returns the installment fee of the payment */
            getSplitoffInstallmentFee: function () {
                // Checking and making sure checkoutConfig data exist and not total 0 dollar
                if (typeof window.checkoutConfig !== 'undefined' &&
                    quote.getCalculatedTotal() > 0) {
                    // Set installment fee from grand total and check format price to be output
                    var installmentFee = quote.getCalculatedTotal() / 4;
                    var format = window.checkoutConfig.priceFormat.pattern;

                    // return with the currency code ($) and decimal setting (default: 2)
                    return format.replace(/%s/g, installmentFee.toFixed(window.checkoutConfig.priceFormat.precision));
                }
            },

            /**
             *  process Splitoff Payment
             */
            continueSplitoffPayment: function () {

                // Added additional validation to check
                if (additionalValidators.validate()) {
                    // start splitoff payment is here
                    var splitoff = window.checkoutConfig.payment.splitoff;

                    // Making sure it using API V2
                    var url = mageUrl.build("splitoff/payment/process");
                    var data = $("#co-shipping-form").serialize();
                    var email = window.checkoutConfig.customerData.email;
                    var ajaxRedirected = false;

                    //CountryCode Object to pass in initialize function.
                    var countryCurrencyMapping ={AUD:"AU", NZD:"NZ", USD:"US",CAD:"CA"};
                    var countryCode = (splitoff.currencyCode in countryCurrencyMapping)? {countryCode: countryCurrencyMapping[splitoff.currencyCode]}:{};

                                                                                                                                                                                                                                                                                                                                                                                                            //Update billing address of the quote
					setBillingAddressAction(globalMessageList);

                        //handle guest and registering customer emails
                        if (!window.checkoutConfig.quoteData.customer_id) {
                            email = document.getElementById("customer-email").value;
                        }

                        data = data + '&email=' + email;

                        $.ajax({
                            url: url,
                            method: 'post',
                            data: data,
                            beforeSend: function () {
                                $('body').trigger('processStart');
                            }
                        }).done(function (response) {
                            // var data = $.parseJSON(response);
                            var data = response;

                            if (data.success && (typeof data.token !== 'undefined' && data.token !== null && data.token.length) ) {
                                //Init or Initialize Splitoff
                                //Pass countryCode to Initialize function
                                if (typeof Splitoff.initialize === "function") {
                                    Splitoff.initialize(countryCode);
                                } else {
                                    Splitoff.init();
                                }

                                //Waiting for all AJAX calls to resolve to avoid error messages upon redirection
                                $("body").ajaxStop(function () {
									ajaxRedirected = true;
                                    splitoffRedirect.redirectToSplitoff(data, data.merchantId);
                                });
								setTimeout(
									function(){
										if(!ajaxRedirected){
											splitoffRedirect.redirectToSplitoff(data, data.merchantId);
										}
									}
								,5000);
                            } else if (typeof data.error !== 'undefined' && typeof data.message !== 'undefined' &&
                                data.error && data.message.length) {
                                globalMessageList.addErrorMessage({
                                    'message': data.message
                                });
                            } else {
                                globalMessageList.addErrorMessage({
                                    'message': data.message
                                });
                            }
                        }).fail(function () {
                            window.location.reload();
                        }).always(function () {
                            customerData.invalidate(['cart']);
                            $('body').trigger('processStop');
                        });
                }
            },

            /**
             * Start popup or redirect payment
             *
             * @param response
             */
            afterPlaceOrder: function () {

                // start splitoff payment is here
                var splitoff = window.checkoutConfig.payment.splitoff;

                // Making sure it using current flow
                var url = mageUrl.build("splitoff/payment/process");

				//Update billing address of the quote
				setBillingAddressAction(globalMessageList);

                $.ajax({
                    url: url,
                    method:'post',
                    success: function (response) {

                        // var data = $.parseJSON(response);
                        var data = response;

                        if (typeof Splitoff.initialize === "function") {
                            Splitoff.initialize({
                                relativeCallbackURL: window.checkoutConfig.payment.splitoff.splitoffReturnUrl
                            });
                        } else {
                            Splitoff.init({
                                relativeCallbackURL: window.checkoutConfig.payment.splitoff.splitoffReturnUrl
                            });
                        }
                        splitoffRedirect.redirectToSplitoff(data, data.merchantId);
                    }
                });
            }
        });
    }
);
