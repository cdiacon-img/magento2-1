<?php

namespace Hic\Integration\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;


class UserData extends \Magento\Framework\Object implements SectionSourceInterface
{

    protected $helper;
   
    protected $logger;
    public function __construct(
        \Hic\Integration\Helper\Helper $helper,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($data);
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {   
        $data = [];        
        if ($this->helper->isEnabled()) {
            $user = $this->getUserData(); 
            if (null !== $user) {
                $data = $user;
            }
        } else {
           $data['disabled' => true];
        }
        return $data;
    }

    protected function getUserData()
    {
        return $this->helper->getUserData();
    }

}
