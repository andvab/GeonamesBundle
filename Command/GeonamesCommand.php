<?php

namespace Andvab\GeonamesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class GeonamesCommand
 * @package Andvab\GeonamesBundle\Command
 */
abstract class GeonamesCommand extends ContainerAwareCommand
{
    /**
     * Get temp dir path
     *
     * @param null $path
     * @return string
     */
    protected function getTempDir($path = null)
    {
        $tempDir = $this->getContainer()->get('kernel')->getCacheDir().'/geonames';
        if (!is_dir($tempDir)) {
            mkdir($tempDir);
        }

        if ($path) {
            $tempDir .= $path;
        }

        return $tempDir;
    }

    /**
     * @param OutputInterface $output
     * @param string          $from
     * @param string          $to
     * @return mixed
     */
    protected function download(OutputInterface &$output, $from, $to)
    {
        $output->writeln('Download '.$from);
        $progress = new ProgressBar($output);
        $progress->setFormat('normal_nomax');
        $step     = 0;
        $ctx      = stream_context_create(
            array(),
            array(
                'notification' => function ($notificationCode, $severity, $message, $messageCode, $bytesTransferred, $bytesMax) use ($output, $progress, &$step) {
                    switch ($notificationCode) {
                        case STREAM_NOTIFY_FILE_SIZE_IS:
                            $progress->start(100);
                            break;
                        case STREAM_NOTIFY_PROGRESS:
                            $newStep = round(($bytesTransferred / $bytesMax) * 100);
                            if ($newStep > $step) {
                                $step = $newStep;
                                $progress->setProgress($step);
                            }
                            break;
                    }
                },
            )
        );

        $file = file_get_contents($from, false, $ctx);
        $progress->finish();
        file_put_contents($to, $file);
        $output->writeln('');

        return $to;
    }

    /**
     * @param string $file
     * @return int
     */
    protected function countRows($file)
    {
        $handler = fopen($file, 'r');
        $count   = 0;

        while (!feof($handler)) {
            fgets($handler);
            $count++;
        }

        fclose($handler);

        return $count;
    }
}
