<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2020/7/15
 * Time: 17:51.
 */

namespace HughCube\BatchFetchIterator;

class CallableBatchFetchIterator extends BatchFetchIterator
{
    /**
     * @var callable An anonymous method to get data
     */
    protected $fetchCallable;

    /**
     * @return callable
     */
    public function getFetchCallable()
    {
        return $this->fetchCallable;
    }

    /**
     * @param callable $fetchCallable
     *
     * @return $this
     */
    public function setFetchCallable($fetchCallable)
    {
        $this->fetchCallable = $fetchCallable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchData($page)
    {
        $callable = $this->fetchCallable;

        return $callable($page);
    }
}
