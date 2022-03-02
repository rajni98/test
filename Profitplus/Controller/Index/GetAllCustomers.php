<?php

namespace Appcoders\Profitplus\Controller\Index;

class GetAllCustomers extends \Magento\Framework\App\Action\Action
{

    protected $_customer;
    protected $_customerFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $customerCollection = $this->_customer->getCollection()
            ->addAttributeToSelect("*")
            ->load();

        foreach ($customerCollection as $customer) {
            $customerData[] = $customer->getData();
        }
        if (!empty($customerData)) {
            $results = array('status' => 'true', 'data' => $customerData);
        } else {
            $results = array('status' => 'error', 'message' => __('No Results Found'));
        }
        $result->setData($results);
        return $result;
    }
}
