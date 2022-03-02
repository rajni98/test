<?php
namespace Appcoders\Profitplus\Controller\Index;

class GetAllImagesByProduct extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->productModel = $productModel;
        $this->imageHelper = $imageHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $productId = $this->getRequest()->getParam('product');
        $_images = $this->productModel->load($productId);
        $mediaList = $_images->getMediaGalleryImages();
        $imagesGallery = array();
        foreach ($mediaList as $media) {
            $imagesGallery[] = array(
                'url' => $this->imageHelper
                    ->init($_images, 'product_page_image_large')
                    ->setImageFile($media->getFile())
                    ->resize('500', '500')
                    ->getUrl(),
                'thumbnail' => $this->imageHelper
                    ->init($_images, 'product_page_image_small')
                    ->setImageFile($media->getFile())
                    ->resize('100', '100')
                    ->getUrl(),
                'position' => $media->getPosition(),
            );
        }
        $result = $this->resultJsonFactory->create();
        $result->setData($imagesGallery);
        return $result;
    }
}
