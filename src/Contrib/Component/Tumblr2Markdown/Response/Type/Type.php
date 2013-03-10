<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

/**
 * Tumblr-typed post data.
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
abstract class Type implements \ArrayAccess, \IteratorAggregate
{
    /**
     * Post data.
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param \SimpleXMLElement $post Post data xml element.
     */
    public function __construct(\SimpleXMLElement $post)
    {
        $this->initData($post);
    }

    // API

    /**
     * Return post data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    // ArrayAccess interface

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    // IteratorAggregate interface

    /**
     * {@inheritdoc}
     *
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    // internal method

    /**
     * Initialize post data.
     *
     * @param \SimpleXMLElement $post Post data xml element.
     */
    protected function initData(\SimpleXMLElement $post)
    {
        foreach (static::dataNames() as $dataName => $isArray) {
            if (isset($post->$dataName)) {
                if ($isArray) {
                    $this->data[$dataName] = array();

                    foreach ($post->$dataName as $data) {
                        $this->data[$dataName][] = (string)$data;
                    }
                } else {
                    $this->data[$dataName] = (string)$post->$dataName;
                }
            } else {
                if ($isArray) {
                    $this->data[$dataName] = array();
                } else {
                    $this->data[$dataName] = null;
                }
            }
        }
    }

    // accessor

    /**
     * Return data names.
     *
     * @return array
     */
    public static function dataNames()
    {
        return array();
    }
}
