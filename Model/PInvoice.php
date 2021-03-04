<?php
namespace MW\PInvoice\Model;
class PInvoice extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'mw_cachetag_mwpi';

    protected $_cacheTag = 'mw_cachetag_mwpi';

    protected $_eventPrefix = 'mw_eventprifixtag_mwpi';

    protected function _construct()
    {
        $this->_init('MW\PInvoice\Model\ResourceModel\PInvoice');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
