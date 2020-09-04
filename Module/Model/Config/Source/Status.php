<?php
namespace Aosom\Marketing\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const STATUS_1  = 1;
    const STATUS_2  = 2;
    const STATUS_3  = 3;
    const STATUS_4  = 4;
    const STATUS_5  = 5;

    protected $itemTypes = [
        self::STATUS_1 => 'Need To Invite',
        self::STATUS_2 => 'Has Invited',
        self::STATUS_3 => 'Need To Check',
        self::STATUS_4 => 'NO PASS',
        self::STATUS_5 => 'PASS'
    ];

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];
        foreach ($this->itemTypes as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        $this->options = $options;

        return $this->options;
    }
}
