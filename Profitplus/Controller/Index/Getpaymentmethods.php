<?php
namespace Appcoders\Profitplus\Controller\Index;

class Getpaymentmethods extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Payment\Model\Config $paymentMethodConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->paymentMethodConfig = $paymentMethodConfig;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    public function execute()
    {

        $payments = $this->paymentMethodConfig->getActiveMethods();
        $result = $this->resultJsonFactory->create();
        $methods = array();

        $payments = $this->paymentMethodConfig->getActiveMethods();
        $methods = array();
        // foreach ($payments as $paymentCode => $paymentModel) {
        //     $paymentTitle = $this->scopeConfig
        //         ->getValue('payment/' . $paymentCode . '/title');
        //     $methods[$paymentCode] = array(
        //         'label' => $paymentTitle,
        //         'code' => $paymentCode,
        //     );
        // }
        // print_r($methods);
        // die('sdgs');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $quote_id = $cart->getQuote()->getEntityId();

        foreach ($payments as $paymentCode => $paymentModel) {
            if ($paymentCode == 'simple') {
                $methods[] = array(
                    'value' => 'Paguelofacil',
                    'code' => $paymentCode,
                    'quote_id' => $quote_id,
                );
            }

            if ($paymentCode == 'checkmo') {
                $methods[] = array(
                    'value' => "Check / Money order",
                    'code' => $paymentCode,
                    'quote_id' => $quote_id,
                );
            }

            if ($paymentCode == 'banktransfer') {
                $methods[] = array(
                    'value' => "Bank Transfer Payment",
                    'code' => $paymentCode,
                    'quote_id' => $quote_id,
                );
            }

            if ($paymentCode == 'cashondelivery') {
                $methods[] = array(
                    'value' => "Cash On Delivery",
                    'code' => $paymentCode,
                    'quote_id' => $quote_id,
                );
            }
        }
        $result->setData($methods);
        return $result;
    }
}
