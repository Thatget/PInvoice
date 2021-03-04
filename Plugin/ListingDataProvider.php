<?php
namespace MW\PInvoice\Plugin;

use MW\PInvoice\Ui\Component\DataProvider as oldListingDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class ListingDataProvider
{

    /**
     * @var \Magento\Framework\Registry
     */

    protected $_registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */

    protected $request;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->_registry = $registry;
        $this->request = $request;
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Get Search Result after plugin
     *
     * @param \MW\PInvoice\Ui\Component\DataProvider $subject
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $result
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    public function afterGetSearchResult(oldListingDataProvider $subject, SearchResult $result)
    {
        if ($result->isLoaded()) {
            return $result;
        }
        return $result;
    }
}
