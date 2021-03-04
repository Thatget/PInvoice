<?php

namespace MW\PInvoice\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column {

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $status = $item[$this->getData('name')];
                if ($status == "0"){
                    $status = "Pending";
                }elseif ($status == "1"){
                    $status = "Processing";
                }elseif ($status == "2"){
                    $status = "Completed";
                }else{
                    $status = "Cancelled";
                }
                $item[$this->getData('name')] = $status;
            }
            return $dataSource;
        }
    }
}
