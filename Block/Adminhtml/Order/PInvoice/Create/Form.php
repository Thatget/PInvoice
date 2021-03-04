<?php

namespace MW\PInvoice\Block\Adminhtml\Order\PInvoice\Create;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items;
use Magento\Tax\Helper\Data as TaxHelper;


class Form extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     * @param TaxHelper|null $taxHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = [],
        ?TaxHelper $taxHelper = null
    ) {
        $data['taxHelper'] = $taxHelper ?? ObjectManager::getInstance()->get(TaxHelper::class);
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('mwpi/pinvoice/save', ['order_id' => $this->getRequest()->getParam('order_id')]);
    }

    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addChild(
            'submit_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => 'Save',
                'class' => 'save submit-button primary' ,
                'onclick' => 'disableElements(\'submit-button\');$(\'pinvoice-custom-form\').submit()',
                'disabled' => false
            ]
        );

        return parent::_prepareLayout();
    }
}
