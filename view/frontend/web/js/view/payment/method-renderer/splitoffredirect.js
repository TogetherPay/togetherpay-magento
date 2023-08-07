/**
 * Magento 2 extensions for TogetherPay Payment
 *
 * @author Splitoff
 * @copyright 2022-2023 TogetherPay https://togetherpay.io
 */
/*browser:true*/
/*global define*/
define(['jquery'],
 function($) {
    'use strict';
    return  {
        redirectToSplitoff: function (data, merchantId) {
            Splitoff.redirect({
				token: data.token,
                merchantId: merchantId
			});
        }
    }

});
