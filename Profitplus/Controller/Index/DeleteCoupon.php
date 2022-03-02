<?php
namespace Appcoders\Profitplus\Controller\Index;

class DeleteCoupon extends \Appcoders\Profitplus\Controller\Index\ApiController
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Checkout\Helper\Cart $checkoutCartHelper,
        \Magento\Customer\Model\Customer $customer,
        \Magento\SalesRule\Model\Rule $saleRule,
        \Magento\SalesRule\Model\Coupon $saleCoupon,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->customer = $customer;
        $this->checkoutCartHelper = $checkoutCartHelper;
        $this->saleRule = $saleRule;
        $this->checkoutSession = $checkoutSession;
        $this->saleCoupon = $saleCoupon;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }

    public function execute()
    {
        $couponCode = $this->request->getParam('coupon_code');
        $result = $this->resultJsonFactory->create();
        if (!$couponCode) {
            $result->setData(['status' => 'error', 'message' => __('Please enter coupon code.')]);
            return $result;
        }
        $cart = $this->checkoutCart;
        //$applyCode = $cart->getQuote()->getCouponCode();
        $applyCode = $this->checkoutSession->getQuote()->getCouponCode();
        if ($couponCode == $applyCode) {
            $carts = $this->checkoutCart->getQuote()->setCouponCode('')->collectTotals()->save();
            $product['subtotal'] = $carts->getSubtotal();
            $product['grandtotal'] = $carts->getGrandTotal();
            $product['totalitems'] = $carts->getItemsCount();
            $result->setData(['status' => 'success', 'message' => $product]);
            return $result;
        } else {
            $result->setData(['status' => 'error', 'message' => __('Invalid coupon code.')]);
            return $result;
        }
    }
}
