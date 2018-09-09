<?php

namespace Andvab\GeonamesBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Andvab\GeonamesBundle\Command\GeonamesCommand;

/**
 * Class GeonamesUpdateAdmin2Command
 * @package Andvab\GeonamesBundle\Command
 */
class GeonamesUpdateAdmin2Command extends GeonamesCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('andvab_geoname:update:admin2');
        $this->setDescription('Update table admin1_codes');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager'));
        $managerAdmin2Codes = $this->getContainer()->get('andvab_geonames.manager.admin2_codes_manager');

        $output->writeln('<info>> Start update table admin2_codes</info>');

        $fileName = 'admin2Codes.txt';
        $file     = $this->download($output, 'http://download.geonames.org/export/dump/'.$fileName, $this->getTempDir('/'.$fileName));

        $output->writeln('Clear the table admin2_codes');

        $managerAdmin2Codes->truncate();

        $output->writeln('Load new data to table admin2codes ...wait');
        $output->writeln('Processing downloaded data...');

        $count = $this->countRows($file);

        $handler = fopen($file, 'r');

        $progress = new ProgressBar($output);
        $progress->setFormat('normal_nomax');
        $progress->start(100);

        $step = 0;

        while (!feof($handler)) {
            $step++;
            $line    = fgets($handler);
            $explode = explode("\t", $line);

            if (count($explode) > 1) {

                $code      = array_key_exists(0, $explode) ? $explode[0] : null;
                $name      = array_key_exists(1, $explode) ? $explode[1] : null;
                $asciiName = array_key_exists(2, $explode) ? $explode[2] : null;
                $geonameId = array_key_exists(3, $explode) ? $explode[3] : null;

                if ($code && $name && $geonameId) {
                    $admin2Code = $managerAdmin2Codes->create($code, $name, $asciiName, $geonameId);

                    $em->persist($admin2Code);
                }

                if (($step % 100) === 0) {
                    $progress->setProgress($step/$count*100);

                    $em->flush();
                    $em->clear();
                }
            }
        }

        $progress->setProgress($step/$count*100);

        $em->flush();
        $em->clear();

        fclose($handler);

        $output->writeln('');
        $output->writeln('Done!');
    }
}
