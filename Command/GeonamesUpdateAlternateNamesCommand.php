<?php

namespace Andvab\GeonamesBundle\Command;

use Andvab\GeonamesBundle\Command\GeonamesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class GeonamesUpdateAlternateNamesCommand
 * @package Andvab\GeonameBundle\Command
 */
class GeonamesUpdateAlternateNamesCommand extends GeonamesCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('andvab_geoname:update:alternatenames');
        $this->setDescription('Update table alternate_names');
        $this->addArgument('languages', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The list of languages.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em                    = $this->getContainer()->get('doctrine.orm.entity_manager');
        $managerAlternateNames = $this->getContainer()->get('andvab_geonames.manager.alternate_names_manager');
        $languages             = $input->getArgument('languages');

        $output->writeln('<info>> Start update table alternate_names</info>');

        $file    = $this->download($output, 'http://download.geonames.org/export/dump/alternateNames.zip', $this->getTempDir('/alternateNames.zip'));
        $zipName = 'zip://'.$file.'#alternateNames.txt';

        $output->writeln('Clear the table alternate_names');

        if (count($languages)) {
            $managerAlternateNames->deleteByLanguages($languages);
        } else {
            $managerAlternateNames->truncate();
        }

        $output->writeln('Load new data to table alternate_names ...wait');
        $output->writeln('Processing downloaded data...');

        $count   = $this->countRows($zipName);

        $handler = fopen($zipName, 'r');

        $progress = new ProgressBar($output);
        $progress->setFormat('normal_nomax');
        $step = 0;

        $progress->start(100);

        while (!feof($handler)) {
            $step++;
            $line    = fgets($handler);
            $explode = explode("\t", $line);

            if (count($explode) > 1) {
                $isoLanguage = array_key_exists(2, $explode) ? $explode[2] : null;
                $isHistoric  = array_key_exists(7, $explode) ? $explode[7] : null;
                $isShortName = array_key_exists(5, $explode) ? $explode[5] : null;

                if (!empty($isoLanguage) && $isoLanguage !== 'link' && $isoLanguage !== 'post' && $isoLanguage !== 'iata' && $isoLanguage !== 'icao' && $isoLanguage !== 'faac' && $isoLanguage !== 'fr_1793' && $isoLanguage !== 'abbr' && $isHistoric !== 1 && $isShortName !== 1) {

                    if (count($languages)) {
                        if (in_array($isoLanguage, $languages)) {
                            $alternateName = $managerAlternateNames->prepareObject($explode);

                            $em->persist($alternateName);
                        }
                    } else {
                        $alternateName = $managerAlternateNames->prepareObject($explode);

                        $em->persist($alternateName);
                    }

                    if (($step % 3000) === 0) {
                        $progress->setProgress($step/$count*100);

                        $em->flush();
                        $em->clear();
                    }
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
