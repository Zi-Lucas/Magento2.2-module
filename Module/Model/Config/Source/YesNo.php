<?php
namespace Aosom\Marketing\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class YesNo implements OptionSourceInterface
{
    const NO  = 0;
    const YES  = 1;

    protected $itemTypes = [
        self::YES => 'Yes',
        self::NO => 'No'
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

    /**
     * Get options
     *
     * @return array
     */
    public function toSimpleOptionArray()
    {
        return $this->itemTypes;
    }
}
