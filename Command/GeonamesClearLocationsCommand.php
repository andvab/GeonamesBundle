<?php

namespace Andvab\GeonamesBundle\Command;

use Andvab\GeonamesBundle\Command\GeonamesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeonamesUpdateCitiesCommand
 * @package Andvab\GeonameBundle\Command
 */
class GeonamesClearLocationsCommand extends GeonamesCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('andvab_geonames:clear:locations');
        $this->setDescription('Clear the table geonames');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('andvab_geonames.manager.geonames_manager')->truncate();

        $output->writeln('Done!');
    }
}
