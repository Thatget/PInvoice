<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MW\PInvoice\Model\ResourceModel;



class PInvoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mw_payment_invoice';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mw_pinvoice_invoice_payment', 'ip_id');
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $object */
        if (!$object->getOrderId() && $object->getOrder()) {
            $object->setOrderId($object->getOrder()->getId());
            $object->setBillingAddressId($object->getOrder()->getBillingAddress()->getId());
        }

        return parent::_beforeSave($object);
    }
}
