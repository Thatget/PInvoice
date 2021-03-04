<?php

namespace MW\PInvoice\Block\Adminhtml\Order\View\Tab;


class PInvoice extends \Magento\Framework\View\Element\Text\ListText implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Payment Invoice');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order Payment Invoices');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
