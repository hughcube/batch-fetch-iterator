<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2020/7/15
 * Time: 17:51
 */

namespace HughCube\BatchFetchIterator;

abstract class BatchFetchIterator implements \Iterator
{
    /**
     * @var int The page number of the data currently retrieved.
     * Default is 1
     */
    protected $page = 1;

    /**
     * @var bool whether to return a single row during each iteration.
     * If false, a whole batch of rows will be returned in each iteration.
     */
    protected $each = true;

    /**
     * @var bool Whether to keep the original key or not
     * Default is false
     */
    protected $preserveKeys = false;

    /**
     * @var array|null the data retrieved in the current batch
     */
    private $_batch;

    /**
     * @var mixed the value for the current iteration
     */
    private $_value;

    /**
     * @var string|int|null the key for the current iteration
     */
    private $_key;

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
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
        $this->_batch = null;
        $this->_value = null;
        $this->_key = null;
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
        if (
            null === $this->_batch
            || !$this->each
            || ($this->each && false === next($this->_batch))
        ) {
            $this->_batch = $this->fetchDataProxy();
            reset($this->_batch);
        }

        if ($this->each) {
            $this->_value = current($this->_batch);

            if (null !== key($this->_batch) && $this->preserveKeys) {
                $this->_key = key($this->_batch);
            } elseif (null !== key($this->_batch) && !$this->preserveKeys) {
                $this->_key = ($this->_key === null ? 0 : $this->_key + 1);
            } else {
                $this->_key = null;
            }
        } else {
            $this->_value = $this->_batch;
            $this->_key = $this->_key === null ? 0 : $this->_key + 1;
        }
    }

    /**
     * Fetches the next batch of data.
     * @return array the data fetched
     */
    protected function fetchDataProxy()
    {
        $data = $this->fetchData($this->page++);
        return empty($data) ? [] : $data;
    }

    /**
     * @param $page
     * @return array
     */
    protected function fetchData($page)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Returns the index of the current dataset.
     * This method is required by the interface [[\Iterator]].
     * @return int the index of the current row.
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Returns the current dataset.
     * This method is required by the interface [[\Iterator]].
     * @return mixed the current dataset.
     */
    public function current()
    {
        return $this->_value;
    }

    /**
     * Returns whether there is a valid dataset at the current position.
     * This method is required by the interface [[\Iterator]].
     * @return bool whether there is a valid dataset at the current position.
     */
    public function valid()
    {
        return !empty($this->_batch);
    }
}
