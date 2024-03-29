<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Helper;

class Media extends \Magento\Swatches\Helper\Media
{
    /**
     * Review attribute option images area inside media folder
     */
    const SWATCH_MEDIA_PATH = 'mageworkshop/detailedreview/attribute_options';

    const SWATCH_THUMBNAIL_NAME = 'review_attribute_swatch_thumbnail';

    /**
     * Need to rewrite this in order to have proper path
     * generated by the \Magento\Swatches\Helper\Media::generateNamePath()
     *
     * @var array $swatchImageTypes
     */
    protected $swatchImageTypes = [self::SWATCH_THUMBNAIL_NAME];

    /**
     * Media swatch path
     *
     * @return string
     */
    public function getSwatchMediaPath()
    {
        return self::SWATCH_MEDIA_PATH;
    }

    /**
     * Media path with swatch_image or swatch_thumb folder
     *
     * @param string $swatchType
     * @return string
     */
    public function getSwatchCachePath($swatchType)
    {
        return self::SWATCH_MEDIA_PATH . '/' . $swatchType . '/';
    }

    /**
     * Setup base image properties for resize
     *
     * @param \Magento\Framework\Image $image
     * @param bool $isSwatch
     * @return $this
     */
    protected function setupImageProperties(\Magento\Framework\Image $image, $isSwatch = true)
    {
        $image->quality(100);
        $image->constrainOnly(true);
        $image->keepAspectRatio(true);
        // $image->keepFrame(true);
        // if (<get the background color from config>):
        // $image->backgroundColor('#FFF');
        // else:
        $image->keepTransparency(true);
        return $this;
    }
}
