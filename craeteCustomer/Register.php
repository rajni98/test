<?php
namespace Sivapp\MobileIntegration\Controller\Index;

class Register extends \Sivapp\MobileIntegration\Controller\Index\ApiController {

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		//\Magento\Framework\App\RequestInterface $requestInterface,
		\Magento\Framework\Event\Manager $eventManager
	) {
		$this->customerSession = $customerSession;
		$this->resultPageFactory = $resultPageFactory;
		$this->customerFactory = $customerFactory;
		$this->storeManager = $storeManager;
		$this->logger = $logger;
		$this->request = $request;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->request = $context->getRequest();
		$this->_eventManager = $eventManager;
		parent::__construct($context);
	}

	public function execute() {

		$result = $this->resultJsonFactory->create();
		if (!$this->customerSession->isLoggedIn()) {
			$params = $this->request->getParams();
			$post = $this->request->getBodyParams();

			if ((null == $post['password']) || (null == $post['email'])) {
				return $result->setData(['status' => "error", 'message' => 'Required feild is missing.']);
			}
			try {
				$customer = $this->customerFactory->create();
				$customer->setPassword($post['password']);
				$customer->setConfirmation($post['password_confirmation'], $post['password']);
				$customer->setFirstname($post['first_name']);
				$customer->setLastname($post['last_name']);
				$customer->setEmail($post['email']);
				$customer->setPassword($post['password']);
				$customer->save();
				$customer->sendNewAccountEmail('registered', '', $this->storeManager->getStore()->getId());

				$customerId = $this->customerSession->getCustomer()->getId();
				$result->setData(['status' => "success", 'message' => 'Account activated successfully.']);
				return $result;
			} catch (\Exception $e) {
				return $result->setData(['status' => "error", 'message' => $e->getMessage()]);
			}
		} else {
			return $result->setData(['status' => "error", 'message' => 'User already logged in.']);
		}
	}
}
