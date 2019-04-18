/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    map: {
        '*': {
            mageWorkshopShoppingCartNotifications: 'MageWorkshop_ShoppingCartNotifications/js/shopping-cart-notifications',
            mageWorkshopShoppingCartNotificationsLib: 'MageWorkshop_ShoppingCartNotifications/js/notify.min',
            mageWorkshopShoppingCartNotificationsStyles: 'MageWorkshop_ShoppingCartNotifications/js/notify-styles'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'MageWorkshop_ShoppingCartNotifications/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};
