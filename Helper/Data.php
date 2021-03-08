<?php

namespace MW\PInvoice\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Framework\ObjectManagerInterface;


class Data extends AbstractHelper {

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var SalesData
     */
    protected $salesData;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        TransactionFactory $transactionFactory,
        InvoiceSender $invoiceSender,
        ManagerInterface $messageManager,
        ObjectManagerInterface $_objectManager,
        SalesData $salesData = null,
        Context $context
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
        $this->messageManager = $messageManager;
        $this->_objectManager = $_objectManager;
        $this->salesData = $salesData ?? $this->_objectManager->get(SalesData::class);
        parent::__construct($context);
    }

    public function generateInvoice($orderId){
        try {
            $order = $this->orderRepository->get($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }
            if(!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the invoice right now.'));
            }
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
//Check capture_case !!!
//            if (!empty($data['capture_case'])) {
//                $invoice->setRequestedCaptureCase($data['capture_case']);
//            }
//            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(false);
            $invoice->getOrder()->setIsInProcess(true);
            $order->addStatusHistoryComment('Automatically INVOICED', false);
            $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());

            $shipment = false;
            if ((int)$invoice->getOrder()->getForcedShipmentWithInvoice()) {
                $shipment = $this->_prepareShipment($invoice);
                if ($shipment) {
                    $transactionSave->addObject($shipment);
                }
            }

            $transactionSave->save();

            // send invoice emails, If you want to stop mail disable below try/catch code
            try {
                if ($this->salesData->canSendNewInvoiceEmail()) {
                    $this->invoiceSender->send($invoice);
                }
            } catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
            }

            if ($shipment) {
                try {
                    if ($this->salesData->canSendNewShipmentEmail()) {
                        $this->shipmentSender->send($shipment);
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                    $this->messageManager->addErrorMessage(__('We can\'t send the shipment right now.'));
                }
            }
            $this->messageManager->addSuccessMessage(__('The invoice has been created.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $invoice;
    }

    /**
     * Prepare shipment
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function _prepareShipment($invoice)
    {
        $itemArr = [];
            $orderItems = $invoice->getOrder()->getItems();
            foreach ($orderItems as $item) {
                $itemArr[$item->getId()] = (int)$item->getQtyOrdered();
            }
        $shipment = $this->shipmentFactory->create(
            $invoice->getOrder(),
            $itemArr
        );
        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }
}
