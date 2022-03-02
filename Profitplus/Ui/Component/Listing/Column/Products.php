<?php

namespace Appcoders\Profitplus\Ui\Component\Listing\Column;

use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Ui\Component\Listing\Columns\Column;

class Products extends Column
{

    protected $_orderRepository;

    protected $_searchCriteria;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        array $components = [],
        array $data = []
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $productArr = [];
                $order = $this->_orderRepository->get($items["entity_id"]);

                foreach ($order->getAllVisibleItems() as $item) {
                    $productArr[] = $item->getQtyOrdered() . "x" . $item->getName();
                }
                $items['products'] = implode(' --- ', $productArr);
            }
            unset($productArr);
        }
        return $dataSource;
    }
}
