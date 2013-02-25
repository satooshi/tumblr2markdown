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
    }

    public function convertXml(array $posts)
    {
        if (!is_dir($this->rootDir)) {
            mkdir($this->rootDir);
        }

        $decoder = new ReadApiDecoder();

        foreach ($posts as $post) {
            $data = $decoder->decode($post);

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

        // mv markdown to under _post directory
        $newPath = $this->rootDir . "/" . $metaMarkdown;
        rename($path, $newPath);

        // clean up tmp files
        unlink($bodyPath);
        unlink($bodyMarkdownPath);
        rmdir($dir);

        return $filename;
    }

    protected function writeMetaMarkdown($dir, \Datetime $dt, $slug, $title, $tag)
    {
        $meta = array(
            '---',
            'layout: post',
            sprintf('title: "%s"', $title),
            sprintf('date: %s', $dt->format('Y-m-d H:i')),
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
