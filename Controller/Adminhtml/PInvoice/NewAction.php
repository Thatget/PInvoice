<?php

namespace MW\PInvoice\Controller\Adminhtml\PInvoice;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;

/**
 * Create new invoice action.
 */
class NewAction extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_invoice';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param InvoiceService $invoiceService
     * @param OrderRepositoryInterface|null $orderRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        InvoiceService $invoiceService,
        OrderRepositoryInterface $orderRepository = null
    )
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->invoiceService = $invoiceService;
        $this->orderRepository = $orderRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(OrderRepositoryInterface::class);
    }

    /**
     * Redirect to order view page
     *
     * @param int $orderId
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _redirectToOrder($orderId)
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }

    /**
     * PInvoice create page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderRepository->get($orderId);

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magento_Sales::sales_order');
            $resultPage->getConfig()->getTitle()->prepend(__('PInvoices'));
            return $resultPage;
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $this->_redirectToOrder($orderId);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, 'Cannot create an invoice.');
            return $this->_redirectToOrder($orderId);
        }
    }
}
