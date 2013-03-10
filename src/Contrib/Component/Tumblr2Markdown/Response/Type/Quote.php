<?php
namespace Contrib\Component\Tumblr2Markdown\Response\Type;

class Quote extends Type
{
    public static function dataNames()
    {
        return array(
            'quote-text' => false,
            'quote-source' => false,
            'tag' => true,
        );
    }
}
