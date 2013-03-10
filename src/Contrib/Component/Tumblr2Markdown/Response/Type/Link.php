<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

class Link extends Type
{
    public static function dataNames()
    {
        return array(
            'link-text' => false,
            'link-url' => false,
            'link-description' => false,
            'tag' => true,
        );
    }
}
