<?php

namespace MW\PInvoice\Controller\Adminhtml\PInvoice;

use Magento\Framework\App\ResponseInterface;

class Change extends \Magento\Backend\App\Action{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        var_dump($this->getRequest()->getParams());
die('ss');
    }
}
