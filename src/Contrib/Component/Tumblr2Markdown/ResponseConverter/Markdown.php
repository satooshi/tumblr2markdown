<?php
namespace Contrib\Component\Tumblr2Markdown\ResponseConverter;

class Markdown
{
    protected $rootDir;
    protected $exclude;

    public function __construct($rootDir, array $exclude = array())
    {
        $this->rootDir = $rootDir;
        $this->exclude = $exclude;

        /*
        if (empty($slug)) {
            continue;
        }

        if (!in_array('blog', $tag)) {
            continue;
        }
        */
    }

    public function convertXml(array $posts)
    {
        if (!is_dir($this->rootDir)) {
            mkdir($this->rootDir);
        }

        foreach ($posts as $post) {
            $data = $this->convertPost($post);

            if (!empty($this->exclude) && $this->exclude($data)) {
                continue;
            }

            $this->write($data);
        }
    }

    protected function exclude(array $data)
    {
        foreach ($this->filter as $key => $exclude) {
            if (is_callable($exclude) && $exclude($data[$key])) {
                return true;
            }
        }

        return false;
    }

    // post data

    protected function convertPost(\SimpleXMLElement $post)
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

    protected function getTag($post)
    {
        $tag = array();

        if (isset($post->tag)) {
            foreach ($post->tag as $t) {
                $tag[] = "$t";
            }
        }

        return $tag;
    }

    protected function getTitle($post)
    {
        $title = "regular-title";
        $xml = isset($post->$title) ? $post->$title : '';

        return "$xml";
    }

    protected function getbody($post)
    {
        $body = "regular-body";
        $xml = isset($post->$body) ? $post->$body : '';

        return "$xml";
    }

    // file IO

    protected function mkdirId($id)
    {
        $dir = $this->rootDir . "/$id";

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    protected function write(array $data)
    {
        $dir          = $this->mkdirId($data['id']);
        $metaMarkdown = $this->writeMetaMarkdown($dir, $data['date'], $data['slug'], $data['title'], $data['tag']);
        $bodyHtml     = $this->writeBodyHtml($dir, $data['body']);

        $this->writeMarkdown($dir, $bodyHtml, $metaMarkdown);
    }

    protected function writeMarkdown($dir, $bodyHtml, $metaMarkdown)
    {
        $filename         = 'body.markdown';
        $bodyMarkdownPath = $dir . "/" . $filename;
        $bodyPath         = $dir . "/" . $bodyHtml;
        $cmd              = sprintf('cd %s; pandoc -f html -t markdown %s -o %s', $dir, $bodyHtml, $filename);

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !empty($output)) {
            throw new \RuntimeException('pandoc failure.');
        }

        $markdown = file_get_contents($bodyMarkdownPath);
        $path     = $dir . "/" . $metaMarkdown;

        file_put_contents($path, "\n" . $markdown, FILE_APPEND | LOCK_EX);

        // clean up
        unlink($bodyPath);
        unlink($bodyMarkdownPath);

        return $filename;
    }

    protected function writeMetaMarkdown($dir, \Datetime $dt, $slug, $title, $tag)
    {
        $meta = array(
            '---',
            'layout: post',
            sprintf('title: "%s"', $title),
            sprintf('date: %s', $dt->format('Y-m-d H:i:s')),
            'comments: false',
            sprintf('categories: [%s]', implode(', ', array_map(function ($t) { return sprintf("'%s'", $t);}, $tag))),
            '---',
        );

        $filename = sprintf("%s-%s.markdown", $dt->format('Y-m-d'), $slug);
        $path     = $dir . "/" . $filename;

        file_put_contents($path, implode("\n", $meta) . "\n");

        return $filename;
    }

    protected function writeBodyHtml($dir, $body)
    {
        $filename = "body.html";
        $path     = $dir . "/" . $filename;

        file_put_contents($path, $body);

        return $filename;
    }
}