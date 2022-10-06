define(
    [
        'uiComponent',
        'ko',
        'Magento_Customer/js/customer-data',
    ],
    function (
        Component,
        ko,
        customerData,
    ) {
        'use strict';

        let cartObservable = customerData.get('cart');

        return Component.extend({
            initialize: function () {
                this._super();
                this.message = ko.observable(cartObservable().message);
                cartObservable.subscribe((function (newCart) {
                    this.message(newCart.message);
                }).bind(this));
            },
        });
    }
);
