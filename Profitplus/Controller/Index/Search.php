<?php
namespace Appcoders\Profitplus\Controller\Index;

class Search extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Helper\Data $directoryHelper
    ) {
        $this->productModel = $productModel;
        $this->imageHelper = $imageHelper;
        $this->stockStateInterface = $stockStateInterface;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $context->getRequest();
        $this->storeManager = $storeManager;
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context);
    }

    public function execute()
    {

        $result = $this->resultJsonFactory->create();
        $searchString = $this->request->getParam('search');
        $searchType = $this->request->getParam('type');
        if (!$searchString || !$searchType) {
            $result->setData(['status' => 'error', 'message' => __('Search string is required.')]);
            return $result;
        }
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $search_criteria = $this->objectManager
            ->create('Magento\Framework\Api\SearchCriteriaBuilder')
            ->addFilter($searchType, $searchString, 'like')->create();
        $productRepository = $this->objectManager->get('Magento\Catalog\Model\ProductRepository');
        $finalData = $productRepository->getList($search_criteria);
        $products = $finalData->getItems();
        $finalResult = array();
        foreach ($products as $product) {
            $product = $this->productModel->load($product->getData('entity_id'));

            $finalResult[] = array(
                'entity_id' => $product->getId(),
                'product_type' => $product->getTypeId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'news_from_date' => $product->getNewsFromDate(),
                'symbol' => "$",
                'news_to_date' => $product->getNewsToDate(),
                'special_from_date' => $product->getSpecialFromDate(),
                'special_to_date' => $product->getSpecialToDate(),
                'description' => $product->getDescription(),
                'short_description' => $product->getShortDescription(),
                'is_in_stock' => $product->isAvailable(),

                'weight' => number_format($product->getWeight(), 2, '.', ''),
                'qty' => $this->stockStateInterface->getStockQty($product->getId(), $product->getStore()->getWebsiteId()),
                'specialprice' => number_format($product->getSpecialPrice(), 2, '.', ''),
                'url_key' => $product->getProductUrl() . '?shareid=' . $product->getId(),
                'image_url' => $this->imageHelper
                    ->init($product, 'product_page_image_small')
                    ->setImageFile($product->getFile())
                    ->resize('250', '250')
                    ->getUrl(),
                'image_url_large' => $this->imageHelper
                    ->init($product, 'product_page_image_large')
                    ->setImageFile($product->getFile())
                    ->resize('500', '500')
                    ->getUrl(),
                'image_url_medium' => $this->imageHelper
                    ->init($product, 'product_page_image_medium')
                    ->setImageFile($product->getFile())
                    ->getUrl(),

            );

            $finalResult[] = $product->getData();
        }
        if ($products) {
            $result->setData(['status' => 'success', 'data' => $finalResult]);
        } else {
            $result->setData(['status' => 'error', 'message' => __('There are no products matching the selection.')]);
        }
        return $result;
    }
}
