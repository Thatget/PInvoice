<?php

namespace MW\PInvoice\Block\Adminhtml\Order\PInvoice;


class Create extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order_invoice';
        $this->_mode = 'create';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('delete');
    }


    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'sales/order/view',
            ['order_id' => $this->getRequest()->getParam('order_id') ? $this->getRequest()->getParam('order_id') : null]
        );
    }
}
