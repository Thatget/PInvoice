<?php

namespace MW\PInvoice\Controller\Adminhtml\PInvoice;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MW\PInvoice\Model\PInvoiceFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\Registry;
use MW\PInvoice\Helper\Data;

class Change extends \Magento\Backend\App\Action {

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_invoice';

    const STATUS_COMPLETE = 2;

    /**
     * @var PInvoiceFactory
     */
    protected $pinvoiceFactory;


    /**
     * @var Data
     */
    protected $_helperData;
    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Change constructor.
     * @param Context $context
     * @param PInvoiceFactory $pinvoiceFactory
     * @param InvoiceService $invoiceService
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PInvoiceFactory $pinvoiceFactory,
        Data $_helperData,
        InvoiceService $invoiceService,
        Registry $registry
    )
    {
        $this->pinvoiceFactory = $pinvoiceFactory;
        $this->_helperData = $_helperData;
        $this->invoiceService = $invoiceService;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();


        $pinvoiceId = $this->getRequest()->getParam('ip_id');
        $orderId = $this->getRequest()->getParam('order_id');
        $pinvoice = $this->pinvoiceFactory->create();
        $pinvoiceC = $pinvoice->load($pinvoiceId);
        $data = $pinvoiceC->getData();
        $data['status'] = $pinvoiceId = $this->getRequest()->getParam('change_to');;
        $pinvoice->setData($data);
        $pinvoice->save();
        try {
            $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            $all = $pinvoice->getCollection()
                ->addFieldToFilter('status', ['eq' => self::STATUS_COMPLETE])
                ->addFieldToFilter('order_id', ['eq' => $orderId])->getData();
            $totalAmount = 0;
            if (!empty($all)){
                foreach ($all as $item) {
                    $totalAmount = $totalAmount + $item['amount'];
                }
            }
            if ($totalAmount >= $order->getGrandTotal()){
                $this->_helperData->generateInvoice($orderId);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __("The invoice can't be saved at this time. Please try again later.")
            );
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}
