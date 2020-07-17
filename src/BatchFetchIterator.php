<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2020/7/15
 * Time: 17:51.
 */

namespace HughCube\BatchFetchIterator;

abstract class BatchFetchIterator implements \Iterator
{
    /**
     * @var int The page number of the data currently retrieved.
     *          Default is 1
     */
    protected $page = 1;

    /**
     * @var bool whether to return a single row during each iteration.
     *           If false, a whole batch of rows will be returned in each iteration.
     */
    protected $each = true;

    /**
     * @var bool Whether to keep the original key or not
     *           Default is false
     */
    protected $preserveKeys = false;

    /**
     * @var array|null the data retrieved in the current batch
     */
    private $batch;

    /**
     * @var mixed the value for the current iteration
     */
    private $value;

    /**
     * @var string|int|null the key for the current iteration
     */
    private $key;

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEach()
    {
        return $this->each;
    }

    /**
     * @param bool $each
     *
     * @return $this
     */
    public function setEach($each)
    {
        $this->each = $each;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPreserveKeys()
    {
        return $this->preserveKeys;
    }

    /**
     * @param bool $preserveKeys
     *
     * @return $this
     */
    public function setPreserveKeys($preserveKeys)
    {
        $this->preserveKeys = $preserveKeys;

        return $this;
    }

    /**
     * constructor.
     */
    public function __construct()
    {
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // make sure cursor is closed
        $this->reset();
    }

    /**
     * Resets the batch query.
     * This method will clean up the existing batch query so that a new batch query can be performed.
     */
    public function reset()
    {
        $this->batch = null;
        $this->value = null;
        $this->key = null;
    }

    /**
     * Resets the iterator to the initial state.
     * This method is required by the interface [[\Iterator]].
     */
    public function rewind()
    {
        $this->reset();
        $this->next();
    }

    /**
     * Moves the internal pointer to the next dataset.
     * This method is required by the interface [[\Iterator]].
     */
    public function next()
    {
        if (!is_array($this->batch) || false === next($this->batch)) {
            $this->batch = $this->fetchDataProxy();
            reset($this->batch);
        }

        if ($this->each) {
            $this->value = current($this->batch);

            if (null !== key($this->batch) && $this->preserveKeys) {
                $this->key = key($this->batch);
            } elseif (null !== key($this->batch) && !$this->preserveKeys) {
                $this->key = ($this->key === null ? 0 : $this->key + 1);
            } else {
                $this->key = null;
            }
        } else {
            $this->value = $this->batch;
            $this->key = $this->key === null ? 0 : $this->key + 1;
        }
    }

    /**
     * Fetches the next batch of data.
     *
     * @return array the data fetched
     */
    protected function fetchDataProxy()
    {
        $data = $this->fetchData($this->page++);

        return empty($data) ? [] : $data;
    }

    /**
     * @param int $page
     *
     * @return array
     */
    protected function fetchData($page)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Returns the index of the current dataset.
     * This method is required by the interface [[\Iterator]].
     *
     * @return int the index of the current row.
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Returns the current dataset.
     * This method is required by the interface [[\Iterator]].
     *
     * @return mixed the current dataset.
     */
    public function current()
    {
        return $this->value;
    }

    /**
     * Returns whether there is a valid dataset at the current position.
     * This method is required by the interface [[\Iterator]].
     *
     * @return bool whether there is a valid dataset at the current position.
     */
    public function valid()
    {
        return !empty($this->batch);
    }
}
