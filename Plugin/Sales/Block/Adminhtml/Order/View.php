<?php
namespace MW\PInvoice\Plugin\Sales\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class View
{
    public function beforeSetLayout(OrderView $subject)
    {
        $subject->addButton(
            'mw_pinvoice_order_button',
            [
                'label' => __('PInvoice'),
                'class' => __('custom-button'),
                'id' => 'mw_pinvoice_order_button',
                'onclick' => 'setLocation(\'' . $subject->getUrl('mwpi/pinvoice/new') . '\')'
            ]
        );
    }
}
