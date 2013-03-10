<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

class Audio extends Type
{
    public static function dataNames()
    {
        return array(
            'audio-caption' => false,
            'audio-player' => false,
            'id3-artist' => false,
            'id3-album' => false,
            'id3-title' => false,
            'tag' => true,
        );
    }
}
