<?php

namespace MW\PInvoice\Controller\Adminhtml\Order\Shipment;

use Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save as ShipmentSave;
class Save extends ShipmentSave{

    /**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(false);
        $transaction = $this->_objectManager->create(
            \Magento\Framework\DB\Transaction::class
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }
}
