<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2013, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         http://www.phptesting.org/
 */

namespace PHPCI\Plugin;

/**
 * Codeception Plugin - Enables full acceptance, unit, and functional testing.
 * @author       Don Gilbert <don@dongilbert.net>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Codeception implements \PHPCI\Plugin
{
    protected $args;
    protected $phpci;

    /**
     * @var string|string[] $xmlConfigFile The path (or array of paths) of an xml config for PHPUnit
     */
    protected $xmlConfigFile;

    public function __construct(\PHPCI\Builder $phpci, array $options = array())
    {
        $this->phpci        = $phpci;

        if (isset($options['config'])) {
            $this->xmlConfigFile = $options['config'];
        }

        if (isset($options['args'])) {
            $this->args = $options['args'];
        }
    }

    /**
     * Runs Codeception tests, optionally using specified config file(s).
     */
    public function execute()
    {
        $success = true;

        // Run any config files first. This can be either a single value or an array.
        if ($this->xmlConfigFile !== null) {
            $success &= $this->runConfigFile($this->xmlConfigFile);
        }

        return $success;
    }

    protected function runConfigFile($configPath)
    {
        if (is_array($configPath)) {
            return $this->recurseArg($configPath, array($this, "runConfigFile"));
        } else {

            $codecept = $this->phpci->findBinary('codecept');

            if (!$codecept) {
                $this->phpci->logFailure('Could not find codeception.');
                return false;
            }

            $cmd = $codecept . ' run -c "%s"';
            $success = $this->phpci->executeCommand($cmd, $this->phpci->buildPath . $configPath);

            return $success;
        }
    }

    protected function recurseArg($array, $callable)
    {
        $success = true;
        foreach ($array as $subItem) {
            $success &= call_user_func($callable, $subItem);
        }
        return $success;
    }
}