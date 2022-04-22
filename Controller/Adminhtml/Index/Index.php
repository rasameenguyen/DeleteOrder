<?php
namespace Magenest\DeleteOrder\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Component\MassAction\Filter as MassActionFilter;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Index extends \Magento\Backend\App\Action
{
    /** @var OrderRepositoryInterface  */
    protected $orderRepository;

    /** @var MassActionFilter  */
    protected $massActionFilter;

    /** @var OrderCollectionFactory  */
    protected $orderCollectionFactory;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param MassActionFilter $massActionFilter
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Context $context
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MassActionFilter $massActionFilter,
        OrderCollectionFactory $orderCollectionFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->massActionFilter = $massActionFilter;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $result = $this->resultRedirectFactory->create();
        try {
            $collection = $this->massActionFilter->getCollection(
                $this->orderCollectionFactory->create()
            );
            $numberOrders = $collection->getSize();
            $collection->walk('delete');
            $message = __('%1 orders was successfully deleted.', $numberOrders);
            $this->messageManager->addSuccessMessage($message);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $this->messageManager->addErrorMessage($message);
            $result->setPath('sales/order/index');
        }
        $result->setPath('sales/order/index');
        return $result;
    }
}