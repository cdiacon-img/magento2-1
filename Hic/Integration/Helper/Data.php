<?php

namespace Hic\Integration\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SETTINGS_ENABLED = 'hiconversion/configuration/enabled';
    const SETTINGS_SITE_ID = 'hiconversion/configuration/site_id';


    protected $scopeConfig;

    protected $objectManager;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
    }
 
    public function getSiteId()
    {
        return $this->scopeConfig->getValue(self::SETTINGS_SITE_ID);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::SETTINGS_ENABLED);
    }

    public function hicPageData()
    {
        $model = $this->objectManager->create('Hic\Integration\Model\Data')
           ->populatePageData();

        if ($model->isProduct()) {
            $model->populateProductData();
        }

        return $model;
    }

}
