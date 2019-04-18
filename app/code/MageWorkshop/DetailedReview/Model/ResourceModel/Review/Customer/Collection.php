<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

/**
 * Report Customers Review collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace MageWorkshop\DetailedReview\Model\ResourceModel\Review\Customer;

class Collection extends \Magento\Reports\Model\ResourceModel\Review\Customer\Collection
{

    /**
     * TODO Fixed a problem with getting the collection if you use the table prefix for database.
     * Join customers
     *
     * @return $this
     */
    protected function _joinCustomers()
    {
        /** @var $connection \Magento\Framework\DB\Adapter\AdapterInterface */
        $connection = $this->getConnection();
        //Prepare fullname field result
        $customerFullname = $connection->getConcatSql(['customer.firstname', 'customer.lastname'], ' ');
        $this->getSelect()->reset(
            \Magento\Framework\DB\Select::COLUMNS
        )->joinInner(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = detail.customer_id',
            []
        )->columns(
            [
                'customer_id' => 'detail.customer_id',
                'customer_name' => $customerFullname,
                'review_cnt' => 'COUNT(main_table.review_id)',
            ]
        )->group(
            'detail.customer_id'
        );
        return $this;
    }
}
