<?php

namespace GreenCheap\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;

class DebuggingDataCollector implements DataCollectorInterface
{
    protected $debugArray;

    /**
     * Constructor.
     *
     * 
     */
    public function __construct($debugArray)
    {
       $this->debugArray = $debugArray ?: false;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        if(!$this->debugArray){
            return ['message' , 'Successful'];
        }
        return [
            'object' => $this->debugArray
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'debugging';
    }
}
