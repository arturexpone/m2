<?php

namespace M2task\BannerManagement\Controller\Banner;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

use M2task\BannerManagement\Model\ResourceModel\Banner\CollectionFactory;
use M2task\BannerManagement\Api\BannerRepositoryInterface;


class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;
    /**
     * @var JsonFactory
     */
    protected $_jsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var CollectionFactory
     */
    private $collection;

    /**
     * @var BannerRepositoryInterface
     */
    private $testReporitory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        JsonFactory $jsonFactory,
        RedirectFactory $resultRedirectFactory,
        CollectionFactory $collectionFactory,
        BannerRepositoryInterface $testReporitory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_jsonFactory = $jsonFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->context = $context;
        $this->collection = $collectionFactory;
        $this->testReporitory = $testReporitory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array|null
     */
    private function getBannersData($data)
    {
        try {
            $currentDate = date('Y.m.d');
            $this->searchCriteriaBuilder
                ->addFilter('banner_id', $data, 'nin')
                ->addFilter('show_start_date', $currentDate, 'lteq')
                ->addFilter('show_end_date', $currentDate, 'gteq');
            $searchCriteria = $this->searchCriteriaBuilder->create();

            $idRes = $this->testReporitory->getList($searchCriteria);
            $idRes = $idRes->getItems();
            $res = [];

            foreach ($idRes as $banner) {
                $res[] = $banner->getData();

            }
            return $res;
        } catch (\Exception $exception) {
            throw new \Error($exception->getMessage());
            // ...
        }
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $reqParams = $this->context->getRequest()->getParams();

        if (isset($reqParams['getBanner']) && isset($reqParams['viewedBanners'])) {
            $res = $this->_jsonFactory->create();
            return $res->setData($this->getBannersData($reqParams['viewedBanners']));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');

        return $resultRedirect;
    }

}
