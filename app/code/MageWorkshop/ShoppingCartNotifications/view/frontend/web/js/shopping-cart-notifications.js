/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko',
    'underscore',
], function ($, Component, customerData, ko, _) {
    'use strict';

    return Component.extend({

        defaults: {
            autoHideDelay: 5000,
            disableDefaultNotification : false,
            notificationsEnabled: 1,
            messageTypesMapping: {
                'success': 'success',
                'notice' : 'info',
                'error'  : 'error',
                'warning': 'warn',
                'default': 'info'
            },
            messages: customerData.get('messages'),
            listens: {
                'messages': 'showMessages'
            }
        },

        initialize: function() {
            this._super();
            // Enable notifications in the left upper corner of the page
            if (!this.notificationsEnabled) {
                return;
            }
            var cookieMessages = _.unique($.cookieStorage.get('mage-messages'), 'text');

            if ($.isArray(cookieMessages) && cookieMessages.length > 0) {
                ko.utils.arrayForEach(cookieMessages, this.notify.bind(this));
            }

            // Remove messages notifications, to prevent duplicating messages
            if (this.disableDefaultNotification) {
                customerData.set('messages', {});
                $.cookieStorage.set('mage-messages', '');
            }
        },

        showMessages: function(newValue) {
            if (typeof newValue.messages !== 'undefined') {
                ko.utils.arrayForEach(newValue.messages, this.notify.bind(this));
            }

            if (this.disableDefaultNotification) {
                // set new value to
                newValue.messages = [];
                return newValue;
            }
        },

        notify: function (message) {
            var type = this.messageTypesMapping.hasOwnProperty(message.type)
                ? message.type
                : 'default';
            $.notify(message.text, {
                'className': this.messageTypesMapping[type],
                'autoHideDelay': this.autoHideDelay,
                'style': 'mageworkshop-message'
            });
        }
    });
});