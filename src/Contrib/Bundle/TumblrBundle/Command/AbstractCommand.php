<?php

namespace Contrib\Bundle\TumblrBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;

    // internal api

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->startStamp();
        $this->console('started.');

        try {
            $returnValue = $this->doWork($input);

            $this->console('success.');
            $this->console($this->stamp());

            return (int)$returnValue;
        } catch (\Exception $e) {
            $this->console('exception occurred.');
            $this->console($this->stamp());
            $this->console($e->getTraceAsString());

            throw $e;
        }
    }

    /**
     * command worker.
     *
     * @param InputInterface $input
     */
    abstract protected function doWork(InputInterface $input);

    // util

    protected function console($message)
    {
        $this->output->writeln(sprintf('%s %s %s', date('Y-m-d H:i:s'), $this->getName(), $message));
    }

    protected function startStamp()
    {
        $this->realUsage     = true;
        $this->startDateTime = microtime(true);
        $this->startPeakMem  = memory_get_peak_usage($this->realUsage);
    }

    protected function stamp()
    {
        $memUnit = 1024;

        return sprintf(
            'elapsed: %s sec, start peak mem: %s kB, end peak mem: %s kB',
            number_format(microtime(true) - $this->startDateTime),
            number_format($this->startPeakMem / $memUnit),
            number_format(memory_get_peak_usage($this->realUsage) / $memUnit)
        );
    }
}
