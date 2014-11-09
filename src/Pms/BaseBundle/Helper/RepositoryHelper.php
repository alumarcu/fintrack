<?php
namespace Pms\BaseBundle\Helper;

abstract class RepositoryHelper
{
    const STATE_FINISHED = -1;
    const STATE_NOT_STARTED = 0;
    const STATE_PROCESSED = 1;

    protected $dataset;
    protected $state;

    public function __construct(array $dataset)
    {
        $this->dataset = $dataset;
        $this->state = self::STATE_NOT_STARTED;
    }

    abstract public function process();

    public function haveState($state)
    {
        return ($this->state === $state);
    }

    public function haveStateBelow($state, $orEqual = false)
    {
        return (($orEqual) ? ($this->state <= $state) : ($this->state < $state));
    }

    public function haveStateAbove($state, $orEqual = false)
    {
        return (($orEqual) ? ($this->state >= $state) : ($this->state > $state));
    }

    public function finish()
    {
        $this->state = self::STATE_FINISHED;
        $this->dataset = null;
        return $this;
    }
}