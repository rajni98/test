<?php
namespace Appcoders\Profitplus\Helper;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Products extends AbstractModel
{
    /**
     * Product Id
     *
     * @var int
     */
    protected $productId;

    /**
     * Product data
     *
     * @var array
     */
    protected $getProduct;

    protected $currentCurrencyCode;

    public function __construct(
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\Review\Model\Review $review,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewFactory,
        \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Magento\Downloadable\Model\Link $link,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlbuilder,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableType,
        \Magento\Directory\Model\Currency $currentCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->productModel = $productModel;
        $this->imageHelper = $imageHelper;
        $this->priceCurrency = $priceCurrency;
        $this->wishlistHelper = $wishlistHelper;
        $this->stockStateInterface = $stockStateInterface;
        $this->review = $review;
        $this->currency = $currency;
        $this->catalogProduct = $catalogProduct;
        $this->wishlistProvider = $wishlistProvider;
        $this->request = $request;
        $this->_link = $link;
        $this->_storeManager = $storeManager;
        $this->_reviewFactory = $reviewFactory;
        $this->_voteFactory = $voteFactory;
        $this->configurableType = $configurableType;
        $this->productRepository = $productRepository;
        $this->_downloadableFile = $downloadableFile;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->urlBuilder = $urlbuilder;
        $this->helper = $helper;
        $this->currentCurrency = $currentCurrency;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param
     * @description : Get the product Review || rating
     * @return array | False
     */

    const XML_PATH_ENABLE = 'custom_settings/general/enable';
    const XML_PATH_LOGIN = 'custom_settings/general/image';
    const XML_PATH_SIDEBAR = 'custom_settings/general/image1';
    public function _ratingCollect($product)
    {

        if ($product) {
            $avg = 0;
            $ratings = array();
            $result = array();

            $collection = $this->_reviewFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $product
            )->setDateOrder();
            $totalRating = array(
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0,
            );
            if (count($collection->getdata()) > 0) {
                foreach ($collection->getItems() as $review) {
                    $ratingCollection = $this->_voteFactory->create()->getResourceCollection()->setReviewFilter(
                        $review->getReviewId()
                    )->addRatingInfo(
                        $this->_storeManager->getStore()->getId()
                    )->setStoreFilter(
                        $this->_storeManager->getStore()->getId()
                    )->load();
                    $review_rating = 0;
                    $rating_method = array();

                    $l = 0;
                    foreach ($ratingCollection as $vote) {
                        $rating_method[$l][$vote->getRatingCode()] = number_format($vote->getPercent() / 20, 1, '.', ',');
                        $review_rating = $vote->getPercent();
                        $ratings[] = $vote->getPercent();
                        $totalRating[($vote->getPercent() / 20)] += 1;
                    }
                    $l++;
                    if ($review_rating) {
                        $rating_by = ($review_rating / 20);
                    }

                    $result['rdetails'][] = array(
                        'title' => $review->getTitle(),
                        'description' => $review->getDetail(),
                        'reviewby' => $review->getNickname(),
                        'rating_by' => $rating_method,
                        'rating_date' => date("d-m-Y", strtotime($review->getCreatedAt())),
                    );
                }
                $avg = array_sum($ratings) / count($ratings);
            }
            $result['rating'] = number_format($avg / 20, 1, '.', ',');
            $result['total_rating'] = array_reverse($totalRating, true);
            return $result;
        } else {
            return false;
        }
    }

    public function getLogoImage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $logo = $this->scopeConfig->getValue(self::XML_PATH_LOGIN, $storeScope);
        return $baseUrl . "appcoders/backendimage/" . $logo;
    }

    public function getLogoImage1()
    {
        // $baseUrl = $this->_storeManager->getStore()->getBaseUrl();

        // $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        // $logo = $this->scopeConfig->getValue(self::XML_PATH_SIDEBAR, $storeScope);

        // return $logourl = "//" . $baseUrl . "pub/media/appcoders/backendimage/" . $logo;
        // print_r($logourl);
        // die;

        return $logo = "images/menu-logo.svg/";

        $folderName = \Appcoders\Profitplus\Model\Config\Backend\Image::UPLOAD_DIR;
        $storeLogoPath = $this->scopeConfig->getValue(
            self::XML_PATH_SIDEBAR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;

        return $imgUrl = $this->urlBuilder
            ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath !== null && $this->_isFile($path)) {
            $url = $imgUrl;
        }

        return $url;
    }
}
