<?php
namespace Contrib\Component\Tumblr2Markdown\Response;

/**
 * Tumblr post attribute.
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class Attribute implements \ArrayAccess, \IteratorAggregate
{
    /**
     * Attribute data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Constructor.
     *
     * @param \SimpleXMLElement $post Post data xml element.
     */
    public function __construct(\SimpleXMLElement $post)
    {
        $this->initAttr($post);
        $this->initIntAttr();

        $this->data['dateime'] = $this->convertDateTimeAttr($this->data);
    }

    // API

    /**
     * Return attribute.
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

    // type

    /**
     * Return whether the post type is regular.
     *
     * @return boolean
     */
    public function isRegular()
    {
        return $this->isType('regular');
    }

    /**
     * Return whether the post type is quote.
     *
     * @return boolean
     */
    public function isQuote()
    {
        return $this->isType('quote');
    }

    /**
     * Return whether the post type is link.
     *
     * @return boolean
     */
    public function isLink()
    {
        return $this->isType('link');
    }

    /**
     * Return whether the post type is photo.
     *
     * @return boolean
     */
    public function isPhoto()
    {
        return $this->isType('photo');
    }

    /**
     * Return whether the post type is video.
     *
     * @return boolean
     */
    public function isVideo()
    {
        return $this->isType('video');
    }

    /**
     * Return whether the post type is audio.
     *
     * @return boolean
     */
    public function isAudio()
    {
        return $this->isType('audio');
    }

    // format

    /**
     * Return whether the post format is html.
     *
     * @return boolean
     */
    public function isHtml()
    {
        return $this->isFormat('html');
    }

    /**
     * Return whether the post format is markdown.
     *
     * @return boolean
     */
    public function isMarkdown()
    {
        return $this->isFormat('markdown');
    }

    // internal method

    /**
     * Initialize attributes from post data.
     *
     * @param \SimpleXMLElement $post
     * @return void
     */
    protected function initAttr(\SimpleXMLElement $post)
    {
        foreach (static::attrNames() as $attrName) {
            $this->data[$attrName] = isset($post[$attrName]) ? (string)$post[$attrName] : null;
        }
    }

    /**
     * Initialize attributes to integer values.
     *
     * @return void
     */
    protected function initIntAttr()
    {
        foreach (static::intAttrNames() as $attrName) {
            if (isset($this->data[$attrName])) {
                $this->data[$attrName] = (int)$this->data[$attrName];
            }
        }
    }

    /**
     * Convert datetime attribute.
     *
     * @param array $data Post attribute.
     * @return \Datetime
     */
    protected function convertDateTimeAttr(array $data)
    {
        if (isset($data['date-gmt'])) {
            return $this->createDateTime($data['date-gmt']);
        }

        if (isset($data['unix-timestamp'])) {
            return $this->createDateTimeFromTimestamp($data['unix-timestamp']);
        }

        return null;
    }

    /**
     * Create DateTime object from "date-gmt" attribute.
     *
     * @param string $dateGmt GMT datetime.
     * @return \DateTime
     */
    protected function createDateTime($dateGmt)
    {
        return $this->setTimezone(new \DateTime($dateGmt));
    }

    /**
     * Create DateTime object from "unix-timestamp" attribute.
     *
     * @param string $timestamp Unix timestamp.
     * @return \DateTime
     */
    protected function createDateTimeFromTimestamp($timestamp)
    {
        return $this->setTimezone(new \DateTime('@' . $timestamp));
    }

    /**
     * Set timezone to DateTime object.
     *
     * "date.timezone" must be set to php.ini configuration.
     *
     * @param \DateTime $datetime
     * @return \DateTime
     */
    protected function setTimezone(\DateTime $datetime)
    {
        $timezone = new \DateTimeZone(ini_get('date.timezone'));
        $datetime->setTimezone($timezone);

        return $datetime;
    }

    /**
     * Return whether the post type equals to argument.
     *
     * @param string $type Type name.
     * @return boolean
     */
    protected function isType($type)
    {
        return $this->data['type'] === $type;
    }

    /**
     * Return whether the post format equals to argument.
     *
     * @param string $format Format name.
     * @return boolean
     */
    protected function isFormat($format)
    {
        return $this->data['format'] === $format;
    }

    // accessor

    /**
     * Return attribute names to be converted to integer value.
     *
     * @return array
     */
    public static function intAttrNames()
    {
        return array(
            'width',
            'height',
            'audio-plays',
        );
    }

    /**
     * Return attribute names.
     *
     * @return array
     */
    public static function attrNames()
    {
        return array(
            'id',             // "30629605905"
            'url',            // "http://satooshi-jp.tumblr.com/post/30629605905"
            'url-with-slug',  // "http://satooshi-jp.tumblr.com/post/30629605905/untitled-by-ryunosuke-on-flickr"
            'type',           // "photo"
            'date-gmt',       // "2012-09-01 03:04:00 GMT"
            'date',           // "Sat, 01 Sep 2012 12:04:00"
            'unix-timestamp', // "1346468640"
            'format',         // "html"
            'reblog-key',     // "18nRJT6h"
            'slug',           // "untitled-by-ryunosuke-on-flickr"
            'width',          // "640" photo only
            'height',         // "360" photo only
            'audio-plays',    // "0"   audio only
        );
    }
}
