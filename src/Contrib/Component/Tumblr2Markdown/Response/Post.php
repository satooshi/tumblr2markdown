<?php
namespace Contrib\Component\Tumblr2Markdown\Response;

use Contrib\Component\Tumblr2Markdown\Response\Type\Audio;
use Contrib\Component\Tumblr2Markdown\Response\Type\Video;
use Contrib\Component\Tumblr2Markdown\Response\Type\Photo;
use Contrib\Component\Tumblr2Markdown\Response\Type\Link;
use Contrib\Component\Tumblr2Markdown\Response\Type\Quote;
use Contrib\Component\Tumblr2Markdown\Response\Type\Regular;

/**
 * Tumblr post data.
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class Post implements \ArrayAccess
{
    /**
     * Post attribute.
     *
     * @var \Contrib\Component\Tumblr2Markdown\Response\Attribute
     */
    protected $attr;

    /**
     * Post data.
     *
     * @var \Contrib\Component\Tumblr2Markdown\Response\Type\Type
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param \SimpleXMLElement $post Post data xml element.
     */
    public function __construct(\SimpleXMLElement $post)
    {
        $this->attr = new Attribute($post);
        $this->data = $this->createPostData($this->attr, $post);
    }

    // ArrayAccess interface

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return in_array($offset, array('attr', 'data'));
    }

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        if ($offset === 'attr') {
            return $this->attr;
        }

        if ($offset === 'data') {
            return $this->data;
        }

        throw new \OutOfRangeException();
    }

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException();
    }

    /**
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException();
    }

    // internal method

    /**
     * Create typed post data.
     *
     * @param Attribute         $attr Post attribute.
     * @param \SimpleXMLElement $post Post data xml element.
     * @return \Contrib\Component\Tumblr2Markdown\Response\Type\Type Typed post data.
     */
    protected function createPostData(Attribute $attr, \SimpleXMLElement $post)
    {
        if ($attr->isRegular()) {
            return new Regular($post);
        }

        if ($attr->isQuote()) {
            return new Quote($post);
        }

        if ($attr->isLink()) {
            return new Link($post);
        }

        if ($attr->isPhoto()) {
            return new Photo($post);
        }

        if ($attr->isVideo()) {
            return new Video($post);
        }

        if ($attr->isAudio()) {
            return new Audio($post);
        }

        return null;
    }

    // accessor

    /**
     * Return post attribute.
     *
     * @return \Contrib\Component\Tumblr2Markdown\Response\Attribute
     */
    public function getAttribute()
    {
        return $this->attr;
    }

    /**
     * Return typed post data.
     *
     * @return \Contrib\Component\Tumblr2Markdown\Response\Type\Type
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return post data.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'attribute' => $this->attr->toArray(),
            'data'      => $this->data->toArray(),
        );
    }
}
