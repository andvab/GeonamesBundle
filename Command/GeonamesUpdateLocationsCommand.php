<?php

namespace Andvab\GeonamesBundle\Command;

use Andvab\GeonamesBundle\Command\GeonamesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class GeonamesUpdateCitiesCommand
 * @package Andvab\GeonameBundle\Command
 */
class GeonamesUpdateLocationsCommand extends GeonamesCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('andvab_geonames:update:locations');
        $this->setDescription('Update table geonames');
        $this->addArgument('countries', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The list of countries.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $countries = $input->getArgument('countries');

        $output->writeln('<info>> Start update table geonames</info>');

        if (count($countries)) {
            foreach ($countries as $country) {
                $country = strtoupper($country);

                $this->loadCountry($country, $output);
            }
        } else {
            $countries = $this->getContainer()->get('doctrine.orm.entity_manager')
                ->getRepository('AndvabGeonamesBundle:Countries')->findAll();

            if (!count($countries)) {
                $output->writeln('Countries list not found');
                $this->getApplication()->find('geo_names:update:countries')->run($input, $output);

                $countries = $this->getContainer()->get('doctrine.orm.entity_manager')
                    ->getRepository('AndvabGeonamesBundle:Countries')->findAll();
            }

            $output->writeln('Clear the table geonames');
            $this->getContainer()->get('andvab_geonames.manager.geonames_manager')->truncate();
            $output->writeln('Cleared');

            foreach ($countries as $country) {
                $this->loadCountry($country->getIso(), $output);
            }
        }

        $output->writeln('Done!');
    }

    protected function loadCountry($countryCode, OutputInterface $output)
    {
        $em              = $this->getContainer()->get('doctrine.orm.entity_manager');
        $featureClasses  = $this->getContainer()->getParameter('andvab_geonames.feature_classes');
        $managerGeonames = $this->getContainer()->get('andvab_geonames.manager.geonames_manager');

        $fileName = $countryCode . '.zip';
        $txtName  = $countryCode . '.txt';

        $fileHeaders = @get_headers('http://download.geonames.org/export/dump/'.$fileName);

        if (count($fileHeaders) && $fileHeaders[0] == 'HTTP/1.1 200 OK') {
            $file = $this->download($output, 'http://download.geonames.org/export/dump/'.$fileName, $this->getTempDir('/'.$fileName));

            $output->writeln('Clear Locations table with country code ' . $countryCode);

            $managerGeonames->deleteByCountry($countryCode);

            $output->writeln('Cleared');

            $output->writeln('Load new data to table geonames for country ' . $countryCode . ' ...wait');
            $output->writeln('Processing downloaded data...');

            $count = $this->countRows('zip://'.$file.'#' . $txtName);

            $handler = fopen('zip://'.$file.'#' . $txtName, 'r');

            $progress = new ProgressBar($output);
            $progress->setFormat('normal_nomax');
            $step = 0;

            $progress->start(100);

            while (!feof($handler)) {
                $step++;
                $line    = fgets($handler);
                $explode = explode("\t", $line);
                if (count($explode) > 1) {

                    $featureClass = array_key_exists(6, $explode) ? $explode[6] : null;

                    if (in_array($featureClass, $featureClasses)) {
                        $geoname = $managerGeonames->prepareObject($explode);

                        $em->persist($geoname);
                    }

                    if (($step % 1000) === 0) {
                        $progress->setProgress($step / $count * 100);

                        $em->flush();
                        $em->clear();
                    }
                }
            }

            $progress->setProgress($step / $count * 100);

            $em->flush();
            $em->clear();

            fclose($handler);

            $output->writeln('');
            $output->writeln('Data to country ' . $countryCode . ' is loaded');
        } else {
            $output->writeln('<error>File ' . $fileName . ' not exist</error>');
        }
    }
}
