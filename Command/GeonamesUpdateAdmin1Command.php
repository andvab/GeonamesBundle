<?php

namespace Andvab\GeonamesBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Andvab\GeonamesBundle\Command\GeonamesCommand;

/**
 * Class GeonamesAdmin1Command
 * @package Andvab\GeonameBundle\Command
 */
class GeonamesUpdateAdmin1Command extends GeonamesCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('andvab_geoname:update:admin1');
        $this->setDescription('Update table admin1_codes');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em                 = $this->getContainer()->get('doctrine.orm.entity_manager');
        $managerAdmin1Codes = $this->getContainer()->get('andvab_geonames.manager.admin1_codes_manager');

        $output->writeln('<info>> Start update table admin1_codes</info>');

        $fileName = 'admin1CodesASCII.txt';
        $file     = $this->download($output, 'http://download.geonames.org/export/dump/'.$fileName, $this->getTempDir('/'.$fileName));

        $output->writeln('Clear the table admin1_codes');

        $managerAdmin1Codes->truncate();

        $output->writeln('Load new data to table admin1codes ...wait');
        $output->writeln('Processing downloaded data...');

        $count   = $this->countRows($file);

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
                    $admin1Code = $managerAdmin1Codes->create($code, $name, $asciiName, $geonameId);

                    $em->persist($admin1Code);
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
