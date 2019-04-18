/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    // Need to initialize tabs and all containers first!
    'jquery',
], function ($) {
    'use strict';

    return function () {
        if (typeof window.mageWorkshop !== 'undefined' &&
            typeof window.mageWorkshop.shoppingCartNotifications !== 'undefined' &&
            window.mageWorkshop.shoppingCartNotifications.loaderEnabled
        ) {
            $.widget('mage.catalogAddToCart', $.mage.catalogAddToCart, {
                options: {
                    processStart: 'processStart',
                    processStop: 'processStop',
                },
            });
        }
    };
});