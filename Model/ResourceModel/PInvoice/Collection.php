<?php
namespace MW\PInvoice\Model\ResourceModel\PInvoice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'ip_id';
    protected $_eventPrefix = 'mwpi_pinvoice_collection';
    protected $_eventObject = 'mwpi_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MW\PInvoice\Model\PInvoice', 'MW\PInvoice\Model\ResourceModel\PInvoice');
    }

}
