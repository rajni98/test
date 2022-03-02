<?php
namespace Appcoders\Profitplus\Controller\Index;

class GetAllRatings extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Appcoders\Profitplus\Helper\Products $productHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->productHelper = $productHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }

    public function execute()
    {

        $product_id = $this->request->getParam('productid');
        $result = $this->resultJsonFactory->create();
        if (!$product_id) {
            $result->setData(['status' => 'error', 'message' => __('Product Id is required.')]);
            return $result;
        }
        $results = $this->productHelper->_ratingCollect($product_id);
        $result->setData(['status' => 'success', 'data' => $results]);
        return $result;
    }
}
