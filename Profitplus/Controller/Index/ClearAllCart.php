<?php
namespace Appcoders\Profitplus\Controller\Index;

class ClearAllCart extends \Appcoders\Profitplus\Controller\Index\ApiController
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $checkoutCart;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    public function execute()
    {

        $result = $this->resultJsonFactory->create();
        $cart = $this->checkoutCart;

        if ($cart->getQuote()->getItemsCount()) {
            $cart->truncate()->save();
        }
        $result->setData(['result' => 'success', 'message' => __('Cart is empty!.')]);
        return $result;
    }
}
