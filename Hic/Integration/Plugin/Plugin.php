<?php

namespace Hic\Integration\Plugin;

class Plugin
{
    private $logger;

    private $objectManager;

    public function __construct (
        \Psr\Log\LoggerInterface $logger,
         \Magento\Framework\ObjectManagerInterface $objectManager        
    ) {
        $this->logger = $logger;
        $this->objectManager = $objectManager;
    }

    public function _getBlockHtml($templateName) 
    {
        return $this->objectManager->create('Hic\Integration\Block\Tag')
            ->setTemplate($templateName)
            ->toHtml();
    }


    public function afterRenderHeadContent(\Magento\Framework\View\Page\Config\Renderer $subject, $html)
    {
        $this->logger->addDebug('rendering head content ');
        $tagPage = $this->_getBlockHtml('headPage.phtml');

        $this->logger->addDebug('tagPage-' . $tagPage);
         
        $tagSession = $this->_getBlockHtml('headSession.phtml'); 
 
        $tagAlways = $this->_getBlockHtml('headAlways.phtml');

        return $tagPage . $tagSession . $tagAlways . $html;
        return $html;
    }
}
