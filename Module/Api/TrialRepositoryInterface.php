<?php
namespace Aosom\Marketing\Api;

use Aosom\Marketing\Api\Data\TrialInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface TrialRepositoryInterface
{
    /**
     * @param TrialInterface $trial
     * @return mixed
     */
    public function save(TrialInterface $trial);

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
     * @param TrialInterface $trial
     * @return mixed
     */
    public function delete(TrialInterface $trial);

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id);

}
