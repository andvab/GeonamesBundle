<?php

namespace Andvab\GeonamesBundle\Command;

use Andvab\GeonamesBundle\Command\GeonamesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeonamesClearAlternateNameCommand
 * @package Andvab\GeonamesBundle\Command
 */
class GeonamesClearAlternateNameCommand extends GeonamesCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('andvab_geonames:clear:alternatenames');
        $this->setDescription('Clear the table alternate_names');
        $this->addArgument('languages', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The list of languages.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages             = $input->getArgument('languages');
        $managerAlternateNames = $this->getContainer()->get('andvab_geonames.manager.alternate_names_manager');

        if (count($languages)) {
            $managerAlternateNames->deleteByLanguages($languages);
        } else {
            $managerAlternateNames->truncate();
        }

        $output->writeln('Done!');
    }
}
