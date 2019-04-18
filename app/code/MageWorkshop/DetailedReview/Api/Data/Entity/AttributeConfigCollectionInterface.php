<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Api\Data\Entity;

/**
 * @api
 */
interface AttributeConfigCollectionInterface extends \IteratorAggregate
{
    /**
     * @param AttributeConfigInterface $attributeConfig
     * @return $this
     * @throws \Exception
     */
    public function addItem(AttributeConfigInterface $attributeConfig);

    /**
     * @param string $attributeType
     * @return false|AttributeConfigInterface
     * @throws \DomainException
     */
    public function getItem($attributeType);
}