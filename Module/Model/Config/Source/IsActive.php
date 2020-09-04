<?php
namespace Aosom\Marketing\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
    const STATUS_DISABLED  = 0;
    const STATUS_ENABLED  = 1;

    protected $itemTypes = [
        self::STATUS_ENABLED => 'Enabled',
        self::STATUS_DISABLED => 'Disabled'
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
