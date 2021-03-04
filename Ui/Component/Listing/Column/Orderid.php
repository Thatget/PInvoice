<?php
namespace MW\PInvoice\Ui\Component\Listing\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Orderid extends Column {

    protected $_orderRepository;

    public function __construct(
        ContextInterface $context,
        OrderRepositoryInterface $orderRepository,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->_orderRepository = $orderRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])){
            foreach ($dataSource['data']['items'] as & $item){
                $order = $this->_orderRepository->get($item['order_id']);
                $orderIncrementId = $order->getIncrementId();
                $item[$this->getData('name')] = $orderIncrementId;
            }
        }
        return $dataSource;
    }
}
