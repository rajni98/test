<?php
namespace Appcoders\Profitplus\Controller\Index;

class DeleteAddress extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
        //\Magento\Framework\App\RequestInterface $requestInterface
    ) {
        $this->addressFactory = $addressFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->addressRepository = $addressRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }

    public function execute()
    {
        $customer = $this->customerSession;
        $addressId = $this->request->getParam('addressId');
        $result = $this->resultJsonFactory->create();
        if (!$addressId) {
            $result->setData(['status' => 'error', 'message' => __('Please select address.')]);
            return $result;
        }
        if ($customer->isLoggedIn()) {
            try {
                $this->addressRepository->deleteById($addressId);
                $result->setData(['status' => 'success', 'message' => __('Address deleted successfully.')]);
                return $result;
            } catch (\Exception $e) {
                $result->setData(['status' => 'error', 'message' => __($e->getMessage())]);
                return $result;
            }
        } else {
            $result->setData(['status' => 'error', 'message' => __('Session expired, Please login again.')]);
            return $result;
        }
    }
}
