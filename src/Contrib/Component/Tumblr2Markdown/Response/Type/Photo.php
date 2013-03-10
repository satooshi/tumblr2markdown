<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

class Photo extends Type
{
    public static function dataNames()
    {
        return array(
            'photo-caption' => false,
            'photo-link-url' => false,
            'photo-url' => true,
            'tag' => true,
        );
    }
}
