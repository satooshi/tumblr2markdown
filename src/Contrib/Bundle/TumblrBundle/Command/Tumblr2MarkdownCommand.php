<?php
namespace Contrib\Bundle\TumblrBundle\Command;

use Contrib\Component\Tumblr2Markdown\Response\Post;

use Contrib\Component\Tumblr2Markdown\ApiClient\V1\TumblrClient;
use Contrib\Component\Tumblr2Markdown\ResponseConverter\Markdown;
use Contrib\Component\Tumblr2Markdown\ResponseConverter\SinatraRedirectionDumper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class Tumblr2MarkdownCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
        ->setName('tumblr:markdown')
        ->setDescription('convert tumblr blog to markdown')
        ->addOption(
            'blogname', // --blogname
            'b', // -b
            InputOption::VALUE_REQUIRED,
            'Your blog name',
            null
        )
        ->addOption(
            'output', // --output
            'o', // -o
            InputOption::VALUE_REQUIRED,
            'Output directory',
            '_posts'
        )
        ->addOption(
            'dump-redirect', // --dump-redirect
            'd', // -d
            InputOption::VALUE_NONE,
            'Dump redirection code for Sinatra'
        )
        ->addOption(
            'dump-data', // --dump-data
            'x', // -x
            InputOption::VALUE_NONE,
            'Dump xml'
        )
        ->addOption(
            'type', // --type
            't', // -t
            InputOption::VALUE_REQUIRED,
            'The type of posts to return. If unspecified or empty, all types of posts are returned. Must be one of text, quote, photo, link, chat, video, or audio.',
            null
        )
        ->addOption(
            'id', // --id
            'i', // -i
            InputOption::VALUE_REQUIRED,
            'A specific post ID to return. Use instead of start, num, or type',
            null
        )
        ->addOption(
            'tagged', // --tagged
            null,
            InputOption::VALUE_REQUIRED,
            'Return posts with this tag in reverse-chronological order (newest first). Optionally specify chrono=1 to sort in chronological order (oldest first).',
            null
        )
        ->addOption(
            'chrono', // --chrono
            null,
            InputOption::VALUE_REQUIRED,
            'Return posts with this tag in reverse-chronological order (newest first). Optionally specify chrono=1 to sort in chronological order (oldest first).',
            null
        )
        ->addOption(
            'search', // --search
            null,
            InputOption::VALUE_REQUIRED,
            'Search for posts with this query.',
            null
        );
    }

    protected function doWork(InputInterface $input)
    {
        $blogName = $input->getOption('blogname');
        $output   = $input->getOption('output');
        $dump     = $input->getOption('dump-data');

        if ($blogName === null) {
            throw new \RuntimeException('--blogname must be specified.');
        }

        $query = array(
            'start' => 0,
            'num'   => 50,
        );

        $query = array_merge($query, $this->buildQuery($input));

        $api = new TumblrClient($blogName);
        $posts = $api->readAll($query);

        $this->console(sprintf('%d posts found', count($posts)));

        if ($dump) {
            foreach ($posts as $post) {
                $data  = new Post($post);
                $array = $data->toArray();

                var_dump($array);
            }
        } else {
            $converter = new Markdown($output);
            $converter->convertXml($posts);

            if ($input->getOption('dump-redirect')) {
                $dumper = new SinatraRedirectionDumper();
                $dumper->convertXml($posts);
            }
        }
    }

    protected function buildQuery(InputInterface $input)
    {
        $option = array(
            'type',
            'id',
            'tagged',
            'chrono',
            'search',
        );

        $query = array();

        foreach ($option as $name) {
            $value = $input->getOption($name);

            if (null !== $value) {
                $query[$name] = $value;
            }
        }

        return $query;
    }
}
