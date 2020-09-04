<?php
namespace Aosom\Marketing\Api;

use Aosom\Marketing\Api\Data\TestreportInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface TestreportRepositoryInterface
{
    /**
     * @param TestreportInterface $report
     * @return mixed
     */
    public function save(TestreportInterface $report);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param TestreportInterface $report
     * @return mixed
     */
    public function delete(TestreportInterface $report);

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id);

}
