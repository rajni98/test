<?php

namespace Appcoders\Profitplus\Controller\Index;

use Magento\Framework\Event;

class GetNewProducts extends \Appcoders\Profitplus\Controller\Index\ApiController
{

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $_eventManager;

    public function __construct(
        Event\Manager $eventManager,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\Pricing\PriceCurrencyInterface $PriceCurrencyInterface,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Helper\Data $directoryHelper
    ) {
        $this->imageHelper = $imageHelper;
        $this->date = $date;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockStateInterface = $stockStateInterface;
        $this->storeManager = $storeManager;
        $this->cache = $cache;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_eventManager = $eventManager;
        $this->scopeConfig = $scopeConfig;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->PriceCurrencyInterface = $PriceCurrencyInterface;
        $this->priceHelper = $priceHelper;
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context);
    }

    public function getBaseCurrencyCode()
    {
        return $this->storeManager->getStore()->getBaseCurrencyCode();
    }

    public function execute()
    {

        $getnewproducts = $this->getnewproducts();
        $result = $this->resultJsonFactory->create();
        $result->setData(['status' => 'success', 'message' => $getnewproducts]);
        return $result;
    }

    public function getnewproducts()
    {

        $storeId = 1;
        $collection = $this->productCollectionFactory->create();
        $todayDate = date('Y-m-d', time());
        $collection->addAttributeToSelect('*')
            ->setPageSize(5)
            ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->setVisibility($this->productVisibility->getVisibleInSiteIds())
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate));
        $collection->getSelect()->order('RAND()');
        $new_productlist = $this->getproductCollection($collection);
        return $new_productlist;
    }

    public function getproductCollection($collection)
    {
        $new_productlist = array();

        foreach ($collection as $product) {
            $specialprice = $product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue();
            $final_price_with_tax = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            if ($specialprice >= $final_price_with_tax) {
                $specialprice = $final_price_with_tax;
            }
            $new_productlist[] = array(
                'entity_id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'news_from_date' => $product->getNewsFromDate() ?: '',
                'news_to_date' => $product->getNewsToDate() ?: '',
                'special_from_date' => $product->getSpecialFromDate() ?: '',
                'special_to_date' => $product->getSpecialToDate() ?: '',
                'image_url' => $this->imageHelper
                    ->init($product, 'product_page_image_large')
                    ->setImageFile($product->getFile())
                    ->resize('300', '300')
                    ->getUrl(),
                'url_key' => $product->getProductUrl(),

                'review' => array(),
                'symbol' => "$",
                'currency_rate' => $this->storeManager->getStore()->getCurrentCurrencyRate(),

                'regular_price_with_tax' => number_format($product->getPrice(), 2, '.', ''),
                'final_price_with_tax' => number_format($product->getFinalPrice(), 2, '.', ''),
                'specialprice' => number_format($specialprice, 2, '.', ''),

            );
        }
        return $new_productlist;
    }

    public function getPermotionalProdcts()
    {
        $getBestseller = $this->getBestsellerProducts();

        $getBestsellerProducts = array(
            'title' => __('Top Products'),
            'count' => count($getBestseller),
            'type' => 'slider',
            'products' => $getBestseller,
        );

        $array = $getBestsellerProducts;
        return $array;
    }
}
