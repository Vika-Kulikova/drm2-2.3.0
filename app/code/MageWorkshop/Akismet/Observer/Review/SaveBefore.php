<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Akismet\Observer\Review;

use Magento\Framework\Event\Observer;
use MageWorkshop\Akismet\Model\AkismetModel;

class SaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;

    /** @var AkismetModel $akismetModel */
    protected $akismetModel;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * SaveBefore constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Action\Context $context
     * @param AkismetModel $akismetModel
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        AkismetModel $akismetModel
    ) {
        $this->akismetModel    = $akismetModel;
        $this->messageManager  = $context->getMessageManager();
        $this->scopeConfig     = $scopeConfig;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {

        if ($this->scopeConfig->getValue(AkismetModel::XML_PATH_MAGEWORKSHOP_AKISMET_GENERAL_ENABLED)
           && $this->scopeConfig->getValue(AkismetModel::XML_PATH_MAGEWORKSHOP_AKISMET_GENERAL_API_KEY)
        ) {
            try {
                /** @var \Magento\Review\Model\Review $review */
                $review = $observer->getEvent()->getData('object');
                $reviewData = [
                    'name' => $review->getNickname(),
                    'comment' => $review->getTitle() . ' ' . $review->getDetail()
                ];
                if ($this->akismetModel->isSpam($reviewData)) {
                    throw new \Exception('Akismet Spam Detected');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }
}
