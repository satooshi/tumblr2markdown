<?php
namespace Contrib\Bundle\TumblrBundle\Command;

use Contrib\Component\Tumblr2Markdown\ApiClient\V1\TumblrClient;
use Contrib\Component\Tumblr2Markdown\ResponseConverter\Markdown;
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
            'your blog name',
            null
        )
        ->addOption(
            'output', // --output
            'o', // -o
            InputOption::VALUE_REQUIRED,
            'output directory',
            '_posts'
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

        $converter = new Markdown($output);
        $converter->convertXml($posts);
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
