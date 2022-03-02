<?php
namespace Appcoders\Profitplus\Controller\Index;

class ClearWishlist extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->wishlistRepository = $wishlistRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $customerId = $customer->getId();
            $wishlist = $this->wishlistRepository->create()->loadByCustomerId($customerId, true);
            if (!$wishlist->getId()) {
                $result->setData(['status' => 'success', 'message' => __('Item not found.')]);
                return $result;
            }
            try {
                $wishlist->delete();
                $wishlist->save();
            } catch (\Exception $e) {
                $result->setData(['status' => 'error', 'message' => __($e->getMessage())]);
                return $result;
            }
            $result->setData(['status' => 'success', 'message' => __('All wishlist items removed.')]);
            return $result;
        } else {
            $result->setData(['status' => 'error', 'message' => __('Session expired, Please login again.')]);
            return $result;
        }
    }
}
