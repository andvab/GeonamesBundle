<?php

namespace Andvab\GeonamesBundle\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Andvab\GeonamesBundle\Command\GeonamesCommand;

/**
 * Class GeonamesUpdateCountriesCommand
 * @package Andvab\GeonamesBundle\Command
 */
class GeonamesUpdateCountriesCommand extends GeonamesCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('andvab_geonames:update:countries');
        $this->setDescription('Update table countries');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $managerCountries = $this->getContainer()->get('andvab_geonames.manager.countries_manager');

        $output->writeln('<info>> Start update table countries</info>');
        $file = $this->download($output, 'http://download.geonames.org/export/dump/countryInfo.txt', $this->getTempDir('/countryInfo.txt'));

        $output->writeln('Clear the table countries');

        $managerCountries->truncate();

        $output->writeln('Load new data to table countries ...wait');

        $count = $this->countRows($file);

        $progress = new ProgressBar($output);
        $progress->setFormat('normal_nomax');
        $step = 0;

        $progress->start(100);

        $handler = fopen($file, 'r');

        while (!feof($handler)) {
            $step++;
            $line = fgets($handler);

            if ($line[0] != '#') {
                $explode = explode("\t", $line);

                if (count($explode) > 1 && !empty($explode[3]) && !empty($explode[5])) {
                    $managerCountries->create($explode);
                }
            }
            $progress->setProgress($step / $count * 100);
        }

        fclose($handler);

        $output->writeln('');
        $output->writeln('Done!');
    }
}
