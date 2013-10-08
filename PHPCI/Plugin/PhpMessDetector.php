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
* PHP Mess Detector Plugin - Allows PHP Mess Detector testing.
* @author       Dan Cryer <dan@block8.co.uk>
* @package      PHPCI
* @subpackage   Plugins
*/
class PhpMessDetector implements \PHPCI\Plugin
{
    /**
     * @var \PHPCI\Builder
     */
    protected $phpci;

    /**
     * @var array
     */
    protected $suffixes;

    /**
     * @var string, based on the assumption the root may not hold the code to be
     * tested, exteds the base path
     */
    protected $path;

    /**
     * @var array - paths to ignore
     */
    protected $ignore;

    /**
     * Array of PHPMD rules. Can be one of the builtins (codesize, unusedcode, naming, design, controversial)
     * or a filenname (detected by checking for a / in it), either absolute or relative to the project root.
     * @var array
     */
    protected $rules;

    /**
     * @param \PHPCI\Builder $phpci
     * @param array $options
     */
    public function __construct(\PHPCI\Builder $phpci, array $options = array())
    {
        $this->phpci = $phpci;

        $this->suffixes = isset($options['suffixes']) ? (array)$options['suffixes'] : array('php');

        $this->ignore = (isset($options['ignore'])) ? (array)$options['ignore'] : $this->phpci->ignore;

        $this->path = (isset($options['path'])) ? $options['path'] : '';

        $this->rules = isset($options['rules']) ? (array)$options['rules'] : array('codesize', 'unusedcode', 'naming');
        foreach ($this->rules as &$rule) {
            if ($rule[0] !== '/' && strpos($rule, '/') !== FALSE) {
                $rule = $this->phpci->buildPath . $rule;
            }
        }
    }

    /**
     * Runs PHP Mess Detector in a specified directory.
     */
    public function execute()
    {
        $ignore = '';
        if (count($this->ignore)) {
            $ignore = ' --exclude ' . implode(',', $this->ignore);
        }

        $suffixes = '';
        if (count($this->suffixes)) {
            $suffixes = ' --suffixes ' . implode(',', $this->suffixes);
        }

        $cmd = PHPCI_BIN_DIR . 'phpmd "%s" text %s %s %s';
        $success = $this->phpci->executeCommand($cmd, $this->phpci->buildPath . $this->path, implode(',', $this->rules), $ignore, $suffixes);
        $errors = count(array_filter(explode(PHP_EOL, $this->phpci->getLastOutput())));
        $this->phpci->storeBuildMeta('phpmd-warnings', $errors);

        return $success;
    }
}
