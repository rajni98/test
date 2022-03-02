<?php
namespace Appcoders\Profitplus\Controller\Index;

use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\EmailNotConfirmedException;

class Login extends \Appcoders\Profitplus\Controller\Index\ApiController
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerUrl $customerHelperData,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerHelperData;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->request = $context->getRequest();
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {

        $username = $this->request->getParam('username');
        $password = $this->request->getParam('password');
        $validate = array();
        $customerinfo = array();
        $result = $this->resultJsonFactory->create();
        try {
            if (($username == null) || ($password == null)) {
                $result->setData(['status' => 'error', 'message' => __('Invalid username and password.')]);
                return $result;
            } /*validations End*/
            else {
/*Customer login portion start*/
                $customer = $this->customerAccountManagement->authenticate($username, $password);
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $this->customerSession->regenerateId();

                if ($this->customerSession->isLoggedIn()) {
                    $customer_data = $this->customerSession->getCustomer()->getData();

                    $customerinfo = array(
                        "id" => $customer_data['entity_id'],
                        "name" => $customer_data['firstname'] . $customer_data['lastname'],
                        "email" => $customer_data['email'],
                    );
                    return $result->setData(['status' => 'success', 'message' => $customerinfo]);
                } else {
                    return $result->setData(['status' => 'error', 'message' => __('Error in authentication')]);
                }
/*Customer login portion end*/
            }
        } catch (EmailNotConfirmedException $e) {
            $value = $this->customerUrl->getEmailConfirmationUrl($username);
            $message = __(
                'This account is not confirmed. Please confirm your account.',
                $value
            );
            return $result->setData(['status' => 'error', 'message' => $message]);
            //   $this->customerSession->setUsername($username);
        } catch (UserLockedException $e) {
            $message = __(
                'The account is locked. Please wait and try again or contact %1.',
                $this->getScopeConfig()->getValue('contact/email/recipient_email')
            );
            return $result->setData(['status' => 'error', 'message' => $message]);
            //  $this->customerSession->setUsername($username);
        } catch (AuthenticationException $e) {
            $message = __('Invalid email or password.');
            return $result->setData(['status' => 'error', 'message' => $message]);
            //  $this->customerSession->setUsername($username);
        } catch (\Exception $ex) {
            $message = __('An unspecified error occurred. Please contact us for assistance.');
            return $result->setData(['status' => 'error', 'message' => $message]);
        }
    }
}
