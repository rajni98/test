<?php
namespace Appcoders\Profitplus\Controller\Index;

class SetAddress extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->addressFactory = $addressFactory;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute()
    {

        $result = $this->resultJsonFactory->create();
        try {
            $params = file_get_contents("php://input");

            $finalJosn = $this->jsonHelper->jsonDecode($params, true);

            $customerId = $finalJosn['userid'];
            if ($customerId) {
                foreach ($finalJosn['address'] as $address) {
                    if (!\Zend_Validate::is($address['firstname'], 'NotEmpty')) {
                        $result->setData(['status' => 'error', 'message' => __('Firstname is required.')]);
                        return $result;
                    }
                    if (!\Zend_Validate::is($address['lastname'], 'NotEmpty')) {
                        $result->setData(['status' => 'error', 'message' => __('Lastname is required.')]);
                        return $result;
                    }
                    if (!\Zend_Validate::is($address['street'], 'NotEmpty')) {
                        $result->setData(['status' => 'error', 'message' => __('Street is required.')]);
                        return $result;
                    }
                    if (!\Zend_Validate::is($address['city'], 'NotEmpty')) {
                        $result->setData(['status' => 'error', 'message' => __('City is required.')]);
                        return $result;
                    }
                    if (!\Zend_Validate::is($address['country_id'], 'NotEmpty') || $address['country_id'] == 'undefined') {
                        $result->setData(['status' => 'error', 'message' => __('Country is required.')]);
                        return $result;
                    }
                    if (!\Zend_Validate::is($address['telephone'], 'NotEmpty')) {
                        $result->setData(['status' => 'error', 'message' => __('Telephone number is required.')]);
                        return $result;
                    }

                    if ($address['firstname'] == null) {
                        $result->setData(['status' => 'error', 'message' => __('Firstname is required.')]);
                        return $result;
                    }
                    $addresssObj = $this->addressFactory->create();
                    $addresssObj->setCustomerId($customerId);
                    $addresssObj->setFirstname($address['firstname']);
                    $addresssObj->setLastname($address['lastname']);
                    $addresssObj->setCountryId($address['country_id']);
                    $addresssObj->setPostcode($address['postcode']);
                    $addresssObj->setCity($address['city']);
                    $addresssObj->setTelephone($address['telephone']);
                    if (isset($address['region'])) {
                        $addresssObj->setRegion($address['region']);
                    } else {
                        $addresssObj->setRegionId($address['region_id']);
                    }
                    $addresssObj->setStreet($address['street']);
                    $addresssObj->setIsDefaultBilling($address['is_default_billing']);
                    $addresssObj->setIsDefaultShipping($address['is_default_shipping']);
                    $addresssObj->setSaveInAddressBook('1');
                    try {
                        $addresssObj->save();
                        $new_array[] = $addresssObj->getId();
                    } catch (\Exception $e) {
                        $result->setData(['status' => 'error', 'message' => __($e->getMessage())]);
                        return $result;
                    }
                }
                if ($new_array) {
                    $result->setData(['status' => 'success', 'message' => __('Address added successfully.')]);
                    return $result;
                }
            } else {
                $result->setData(['status' => 'error', 'message' => __('No record found.')]);
                return $result;
            }
        } catch (\Exception $e) {
            $result->setData(['status' => 'error', 'message' => __($e->getMessage())]);
            return $result;
        }
    }
}
