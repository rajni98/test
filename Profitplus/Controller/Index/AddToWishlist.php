<?php
namespace Appcoders\Profitplus\Controller\Index;

class AddToWishlist extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->wishlistRepository = $wishlistRepository;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }
    public function execute()
    {

        $result = $this->resultJsonFactory->create();
        $response = array();
        if (!$this->scopeConfig->getValue('wishlist/general/active')) {
            $response['status'] = 'error';
            $response['message'] = __('Wishlist has been disabled by admin.');
        }
        if (!$this->customerSession->isLoggedIn()) {
            $response['status'] = 'error';
            $response['message'] = __('Session expired, Please login again.');
        }
        if (empty($response)) {
            $customer = $this->customerSession->getCustomer();
            $customerId = $customer->getId();
            $wishlist = $this->wishlistRepository->create()->loadByCustomerId($customerId, true);

            if (!$wishlist) {
                $response['status'] = 'error';
                $response['message'] = __('Unable to create wishlist.');
            } else {
                $productId = (int) $this->request->getParam('product');
                if (!$productId) {
                    $response['status'] = 'error';
                    $response['message'] = __('Product not found.');
                } else {
                    $product = $this->productRepository->getById($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response['status'] = 'error';
                        $response['message'] = __('Cannot specify product.');
                    } else {
                        try {
                            $product = $this->productRepository->getById($productId);
                            $wishlist = $this->wishlistRepository->create()->loadByCustomerId($customerId, true);
                            $wishlist->addNewItem($product);
                            $wishlist->save();
                            $message = __('%1 has been added to your wishlist.', $product->getName());
                            $response['status'] = 'success';
                            $response['message'] = $message;
                        } catch (\Exception $e) {
                            $response['status'] = 'error';
                            $response['message'] = __('An error occurred while adding item to wishlist: %1.', $e->getMessage());
                        } catch (\Exception $e) {
                            $response['status'] = 'error';
                            $response['message'] = __('An error occurred while adding item to wishlist.');
                        }
                    }
                }
            }
        }
        $result->setData([$response]);
        return $result;
    }
}
