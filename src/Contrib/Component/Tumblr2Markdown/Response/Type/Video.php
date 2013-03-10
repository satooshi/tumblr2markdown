<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

class Video extends Type
{
    public static function dataNames()
    {
        return array(
            'video-source' => false,
            'video-caption' => false,
            'video-player' => true,
            'tag' => true,
        );
    }
}
