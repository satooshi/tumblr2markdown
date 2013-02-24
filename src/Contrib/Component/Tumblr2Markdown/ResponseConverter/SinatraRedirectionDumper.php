<?php
namespace Contrib\Component\Tumblr2Markdown\ResponseConverter;

class SinatraRedirectionDumper
{
    protected $redirectTo;

    public function convertXml(array $posts)
    {
        $this->redirectTo = array();
        $decoder = new ReadApiDecoder();

        foreach ($posts as $post) {
            $data = $decoder->decode($post);
            $this->storeRedirectTo($data);
        }

        $this->dumpRedirectTo();
    }

    protected function storeRedirectTo(array $data)
    {
        // /xxxxxxx
        // /xxxxxxx/your-slug
        // ->
        // /blog/2013/02/24/your-slug/

        $date       = $data['date'];
        $slugUrl    = $data['id'] . '/' . $data['slug'];
        $redirectTo = sprintf('/blog/%s/%s/', $date->format('Y/m/d'), $data['slug']);

        $this->redirectTo[$data['id']] = $redirectTo;
        $this->redirectTo[$slugUrl]    = $redirectTo;
    }

    protected function dumpRedirectTo()
    {
        foreach ($this->redirectTo as $old => $new) {
            echo sprintf('
get(/%s/) do
  redirect "%s", 301
end
', str_replace('/', '\/', $old), $new);
        }
    }
}
