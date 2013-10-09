<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2013, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         http://www.phptesting.org/
 */

namespace PHPCI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate console command - Reads the database and generates models and stores.
 * @author       Dan Cryer <dan@block8.co.uk>
 * @package      PHPCI
 * @subpackage   Console
 */
class UpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('phpci:update')
            ->setDescription('Update the database to reflect modified models.');
    }

    /**
     * Generates Model and Store classes by reading database meta data.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Update the database:
        $gen = new \b8\Database\Generator(\b8\Database::getConnection(), 'PHPCI', './PHPCI/Model/Base/');
        $gen->generate();
    }
}
