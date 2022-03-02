<?php
namespace Appcoders\Profitplus\Controller\Index;

class SubscribeNewsletter extends \Appcoders\Profitplus\Controller\Index\ApiController
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
        $this->request = $context->getRequest();
        $this->customerRepository = $customerRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $subscriber = $objectManager->create('Magento\Newsletter\Model\SubscriberFactory');
        $subcribe = $this->request->getParam('subscribe');
        $email = $this->request->getParam('email');
        if ($subcribe == 1) {
            $subscriber->create()->subscribe($email);
            echo json_encode(['status' => "success", "message" => "Successfully subscribed"]);
            exit;
        } else {
            $subscriber->create()->loadByEmail($email)->unsubscribe();
            echo json_encode(['status' => "success", "message" => "Successfully Unsubscribed"]);
            exit;
        }
    }
}
