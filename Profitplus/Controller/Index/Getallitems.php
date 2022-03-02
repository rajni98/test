<?php
namespace Appcoders\Profitplus\Controller\Index;

class Getallitems extends \Appcoders\Profitplus\Controller\Index\ApiController
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Catalog\Model\Product $catalog,
        \Magento\Directory\Model\Currency $currentCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->customer = $customer;
        $this->catalog = $catalog;
        $this->productModel = $productModel;
        $this->currentCurrency = $currentCurrency;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->scopeConfig = $scopeConfig;
        $this->wishlistRepository = $wishlistRepository;
        $this->customerSession = $customerSession;
        $this->wishlistHelper = $wishlistHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }
    public function execute()
    {

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $om->get('Magento\Customer\Model\Session');
        $result = $this->resultJsonFactory->create();

        try {
            $baseCurrency = $this->storeManager->getStore()->getBaseCurrencyCode();
            $currentCurrency = "$";
            $product_model = $this->catalog;
            $quoteId = $_GET['quote_id'];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $quoteFactory = $objectManager->create('\Magento\Quote\Model\QuoteFactory');
            $q = $quoteFactory->create()->load($quoteId);
            $cart = $this->checkoutCart;
            $quote = $cart->getQuote();
            foreach ($q->getAllVisibleItems() as $item) {
                $productdata = $this->productModel->load($item->getProductId());

                $final_params['qty'] = $item->getQty();

                if ($productdata) {
                    $final_params['product'] = $item->getProductId();
                    if ($customerSession->isLoggedIn()) {
                        $this->checkoutCart->addProduct($item->getProductId(), $item->getQty());
                        $this->checkoutCart->save();
                    }
                }
            }

            foreach ($q->getAllVisibleItems() as $item) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $singleProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                $productName = array();
                $productName['cart_item_id'] = $item->getId();
                $productName['id'] = $item->getProductId();
                $productName['sku'] = $item->getSku();
                $productName['qty'] = $item->getQty();
                $productName['Name'] = $item->getProduct()->getName();
                $productName['total_price'] = $item->getPrice() * $item->getQty();
                $productName['Price'] = $item->getPrice();

                $productName['image'] = $this->imageHelper
                    ->init($singleProduct, 'product_page_image_large')
                    ->setImageFile($singleProduct->getFile())
                    ->resize('100', '100')
                    ->getUrl();

                $product['product'][] = $productName;
            }
            $product['subtotal'] = $q->getSubtotal();
            $product['grandtotal'] = $q->getGrandTotal();
            $product['totalitems'] = $q->getItemsCount();
            $product['symbol'] = '$';

            $result->setData(['status' => 'success', 'message' => $product]);
            return $result;
        } catch (\Exception $e) {
            $result->setData(['status' => 'error', 'message' => __($e->getMessage())]);
            return $result;
        }
    }
}
