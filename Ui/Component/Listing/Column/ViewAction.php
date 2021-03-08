<?php

namespace MW\PInvoice\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use MW\PInvoice\Model\PInvoiceFactory;

/**
 * Class ViewAction
 */
class ViewAction extends Column
{

    const STATUS_PENDING = 0;

    const STATUS_PROCESSING = 1;

    const STATUS_COMPLETE = 2;

    const STATUS_CANCELLED  = 3;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var PInvoiceFactory
     */
    protected $pinvoiceFactory;

    /**
     * ViewAction constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param PInvoiceFactory $pinvoiceFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        PInvoiceFactory $pinvoiceFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->pinvoiceFactory = $pinvoiceFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['ip_id'])) {
                    $PinvoiceData = $this->pinvoiceFactory->create()->load($item['ip_id']);
                    $dataStatus = $PinvoiceData->getData('status');
                    $changeUrlPath = $this->getData('config/changeUrlPath') ?: '#';
                    if ($dataStatus == (string)self::STATUS_PENDING) {
                        $item[$this->getData('name')] = [
                            'edit' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $changeUrlPath,
                                    [
                                        'ip_id' => $item['ip_id'],
                                        'change_to' => 1,
                                        'order_id' => $item['order_id']
                                    ]
                                ),
                                'label' => __('Processing')
                            ],
                            'update2' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $changeUrlPath,
                                    [
                                        'ip_id' => $item['ip_id'],
                                        'change_to' => 2,
                                        'order_id' => $item['order_id']
                                    ]
                                ),
                                'label' => __('Completed')
                            ],
                            'update3' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $changeUrlPath,
                                    [
                                        'ip_id' => $item['ip_id'],
                                        'change_to' => 3,
                                        'order_id' => $item['order_id']
                                    ]
                                ),
                                'label' => __('Cancelled')
                            ]
                        ];
                    }elseif ($dataStatus == (string)self::STATUS_PROCESSING) {
                        $item[$this->getData('name')] = [
                            'update2' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $changeUrlPath,
                                    [
                                        'ip_id' => $item['ip_id'],
                                        'change_to' => 2,
                                        'order_id' => $item['order_id']
                                    ]
                                ),
                                'label' => __('Completed')
                            ],
                            'update3' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $changeUrlPath,
                                    [
                                        'ip_id' => $item['ip_id'],
                                        'change_to' => 3,
                                        'order_id' => $item['order_id']
                                    ]
                                ),
                                'label' => __('Cancelled')
                            ]
                        ];
                    }else {
                        $item[$this->getData('name')] = '';
                    }
                }
            }
        }

        return $dataSource;
    }
}
