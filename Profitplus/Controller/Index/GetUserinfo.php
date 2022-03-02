<?php
namespace Appcoders\Profitplus\Controller\Index;

class GetUserinfo extends \Appcoders\Profitplus\Controller\Index\ApiController
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->customerSession->isLoggedIn()) {
            $info = array();
            $customer = $this->customerSession->getCustomer();
            $customerData = $this->customerRepository->getById($customer->getId());
            $info['firstname'] = $customer->getFirstname();
            $info['lastname'] = $customer->getLastname();
            $info['email'] = $customer->getEmail();

            $result->setData(['status' => 'success', 'data' => $info]);
            return $result;
        } else {
            $result->setData(['status' => 'error', 'message' => __('Session expired , Please login again.')]);
            return $result;
        }
    }
}
