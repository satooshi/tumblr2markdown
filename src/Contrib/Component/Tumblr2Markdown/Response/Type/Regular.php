<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

class Regular extends Type
{
    public static function dataNames()
    {
        return array(
            'regular-title' => false,
            'regular-body' => false,
            'tag' => true,
        );
    }
}
