<?php
namespace Appcoders\Profitplus\Controller\Index;

class Getshippingmethods extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    protected $_checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->currency = $currency;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function getCheckOutSession()
    {
        return $this->_checkoutSession;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $countryId = $this->getRequest()->getParam('country_id');
        $postCode = $this->getRequest()->getParam('zipcode');
        $currentCurrency = $this->currency;

        $session = $this->getCheckOutSession();
        $address = $session->getQuote()->getShippingAddress();
        $address->setCountryId($countryId)
            ->setPostcode($postCode)
            ->setSameAsBilling(1);

        $rates = $address
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->getGroupedAllShippingRates();
        $shipMethods = [];
        foreach ($rates as $carrier) {
            foreach ($carrier as $rate) {
                $shipMethods[] = array(
                    'code' => $rate->getData('code'),
                    'value' => $rate->getData('carrier_title'),
                    'price' => $rate->getData('price'),
                );
            }
        }

        $result->setData($shipMethods);
        return $result;
    }
}
