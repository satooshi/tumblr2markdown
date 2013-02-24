<?php
namespace Contrib\Component\Tumblr2Markdown\ResponseConverter;

class ReadApiDecoder
{
    public function decode(\SimpleXMLElement $post)
    {
        return array(
            'id'            => (string)$post['id'],
            'url-with-slug' => (string)$post['url-with-slug'],
            'date'          => new \DateTime((string)$post['date']),
            'slug'          => (string)$post['slug'],
            'tag'           => $this->getTag($post),
            'title'         => $this->getTitle($post),
            'body'          => $this->getbody($post),
        );
    }

    protected function getTag(\SimpleXMLElement $post)
    {
        $tag = array();

        if (isset($post->tag)) {
            foreach ($post->tag as $t) {
                $tag[] = "$t";
            }
        }

        return $tag;
    }

    protected function getTitle(\SimpleXMLElement $post)
    {
        $title = 'regular-title';
        $xml = isset($post->$title) ? $post->$title : '';

        return "$xml";
    }

    protected function getbody(\SimpleXMLElement $post)
    {
        $body = 'regular-body';
        $xml = isset($post->$body) ? $post->$body : '';

        return "$xml";
    }
}
