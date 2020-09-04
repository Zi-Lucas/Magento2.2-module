<?php
namespace Aosom\Marketing\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;

class Trial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aosom_trial_report','trial_id');
    }


}
