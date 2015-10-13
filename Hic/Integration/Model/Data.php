<?php

namespace Hic\Integration\Model;

class Data extends \Magento\Framework\Model\AbstractModel
{ 
   
  
    protected $request;

    protected $catalogData;

    protected $productHelper;

    protected $cartHelper;

    protected $searchCriteriaBuilder;
   
    protected $filterBuilder;

    protected $customerRepository;

    protected $customerSession;

    protected $orderCollectionFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Resource\Order\CollectionFactory $orderCollectionFactory
   ) {
        $this->request = $request;
        $this->catalogData = $catalogData;
        $this->productHelper = $productHelper;
        $this->cartHelper = $cartHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    protected function _getRoute()
    {
        return $this->request->getFullActionName();
    }


    public function isProduct()
    {
        return 'catalog_product_view' == $this->_getRoute();
    }

    public function populatePageData()
    {
        $crumb = array();
        foreach ($this->catalogData->getBreadcrumbPath() as $item) {
          $crumb[] = $item['label'];
        }

        $this->setPage(
            array(
                'route' => $this->_getRoute(),
                'bc' => $crumb
            )
        );
        return $this;
    }

    protected function _getCategoryNames($product)
    {
        $catIds = $product->getCategoryIds();
    //    $this->filterBuilder
      //      ->setField('id')
        
    }

    //TODO: we may need to limit this further but this is one to one with 1.x magento extension
    protected function _getOrders($customerId)
    {
        return $this->orderCollectionFactory->create()
            ->addAttributeToFilter('customer_id', $customerId);
    }

    public function populateProductData()
    {
        $currentProduct = $this->catalogData->getProduct();
        if ($currentProduct) {
            $data['cat'] = $this->getCategory();
            $data['id']  = $currentProduct->getId();
            $data['nm']  = $currentProduct->getName();
            $data['url'] = $this->productHelper->getProductUrl($currentProduct);
            $data['sku'] = $currentProduct->getSku();
            $data['bpr'] = $currentProduct->getPrice();
            $data['img'] = $this->productHelper->getImageUrl($currentProduct);
            $this->setProduct($data);
        }
        return $this;
    }

    public function populateCartData()
    {
        $cartQuote = $this->cartHelper->getQuote();
        if ($cartQuote->getItemsCount() > 0) {
            $data = array();
            if ($cartQuote->getSubtotal()) {
                $data['st'] = (float)$cartQuote->getSubtotal();
            }
            if ($cartQuote->getGrandTotal()) {
                $data['tt'] = (float)$cartQuote->getGrandTotal();
            }
            if ($cartQuote->getItemsCount()) {
                $data['qt'] = (float)$cartQuote->getItemsQty();
            }
            if ($cartQuote->getStoreCurrencyCode()) {
                $data['cu'] = $cartQuote->getStoreCurrencyCode();
            }
          //  $data['li'] = $this
           //     ->_getCartItems($cartQuote->getAllVisibleItems(), false);
            $this->setCart($data);
                
        }
        return $this;
    }

    

    public function populateUserData()
    {
        $data = array();
	$data['auth'] = $this->customerSession->isLoggedIn();
	$data['ht'] = false;
	$data['nv'] = true;
	$data['cg'] = $this->customerSession->getCustomerGroupId();
        $customerId = $this->customerSession->getId();
	if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            if ($customer) {
	        $orders = $this->_getOrders($customerId);
                if ($orders) {
	    	    $data['ht'] = $orders->getSize() > 0;
	        }
	        if ($customer->getDob()) {
	            $data['bday'] = $customer->getDob();
	        }
	        if ($customer->getGender()) {
	            $data['gndr'] = $customer->getGender();
	        }
	        if ($customer->getEmail()) {
	            $data['email'] = $customer->getEmail();
	        }
	        $data['id'] = $customer->getId();
	        $data['nv'] = false;
	        $data['nm'] = trim($customer->getFirstname()) . ' ' . trim($customer->getLastname());
	        $data['since'] = $customer->getCreatedAt();
	    }
        }
	$this->setUser($data);
        
        return $this;
    }
}
