<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Base class definition
 *
 * PHP version 5
 *
 * @category  Monitoring
 * @package   PHP_Icinga
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://pear.random-host.com/
 */
namespace randomhost\Icinga\Checks;

use randomhost\Icinga\Check as Check;

/**
 * Base class for Icinga Plugins
 *
 * @category  Monitoring
 * @package   PHP_Icinga
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
abstract class Base implements Check
{
    /**
     * Help message for this plugin.
     *
     * @var string
     */
    protected $help = '';

    /**
     * Each character in this string will be used as option characters and
     * matched against options passed to the script starting with a single
     * hyphen (-). For example, an option string "x" recognizes an option -x.
     * Only a-z, A-Z and 0-9 are allowed.
     *
     * @var string
     */
    protected $shortOptions = '';

    /**
     * An array of options. Each element in this array will be used as option
     * strings and matched against options passed to the script starting with
     * two hyphens (--). For example, an longopts element "opt" recognizes an
     * option --opt.
     *
     * @var array
     */
    protected $longOptions = array('help');

    /**
     * An array of option / argument pairs.
     *
     * @var array
     */
    protected $options = array();

    /**
     * An array of required option / argument pairs.
     *
     * @var array
     */
    protected $requiredOptions = array();

    /**
     * Icinga plugin output.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Icinga return code.
     *
     * @var integer
     */
    protected $code = Check::SERVICE_STATE_UNKNOWN;

    /**
     * Performs the Icinga check.
     *
     * @return void
     */
    public function run()
    {
        $this->preCheck();
        $this->check();
        $this->postCheck();
    }

    /**
     * Reads command line options and performs pre-check tasks.
     * 
     * @return void
     */
    protected function preCheck()
    {
        $this->options = getopt($this->shortOptions, $this->longOptions);
        $this->displayHelp();
        $this->checkRequiredParameters();
    }

    /**
     * Must be implemented by all child classes and contains the main
     * Icinga plugin logic.
     * 
     * @return void
     */
    protected abstract function check();

    /**
     * Outputs the Icinga plugin output and exits with the set Icinga return code.
     * 
     * @return void
     */
    protected function postCheck()
    {
        echo $this->getMessage();
        exit($this->getCode());
    }

    /**
     * Checks if all required parameters are set.
     * 
     * @return void
     */
    protected function checkRequiredParameters()
    {
        $missing = array_diff(
            $this->requiredOptions, array_keys($this->options)
        );
        if (0 !== count($missing)) {
            echo sprintf(
                'Missing required parameters: %s' . PHP_EOL,
                implode(', ', $missing)
            );
            exit(Check::SERVICE_STATE_UNKNOWN);
        }
    }

    /**
     * Displays a help message and exits.
     * 
     * @return void
     */
    protected function displayHelp()
    {
        if (array_key_exists('help', $this->options)) {
            echo $this->getHelp();
            exit(0);
        }
    }

    /**
     * Set help message for this plugin.
     *
     * @param string $help Help message text
     * 
     * @return void
     */
    protected function setHelp($help)
    {
        $this->help = (string)$help;
    }

    /**
     * Return help message for this plugin.
     *
     * @return string
     */
    protected function getHelp()
    {
        return (string)$this->help;
    }
    
    /**
     * Set short options.
     *
     * Each character in this string will be used as option characters.
     * Only a-z, A-Z and 0-9 are allowed.
     *
     * @param string $options Option characters
     *
     * @return void
     */
    protected function setShortOptions($options)
    {
        $this->shortOptions = (string)$options;
    }

    /**
     * Return short options.
     *
     * @return string
     */
    protected function getShortOptions()
    {
        return (string)$this->shortOptions;
    }

    /**
     * Set long options.
     *
     * Each element in this array will be used as option strings.
     *
     * @param array $options Array with option strings
     * 
     * @return void
     */
    protected function setLongOptions(array $options)
    {
        $this->longOptions = array_merge($this->longOptions, $options);
    }

    /**
     * Return long options.
     *
     * @return array
     */
    protected function getLongOptions()
    {
        return (string)$this->longOptions;
    }

    /**
     * Return an array of option / argument pairs.
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * Set required options.
     *
     * @param array $options Array with option strings
     *
     * @return void
     */
    protected function setRequiredOptions(array $options)
    {
        $this->requiredOptions = $options;
    }

    /**
     * Return required options.
     *
     * @return array
     */
    protected function getRequiredOptions()
    {
        return (string)$this->requiredOptions;
    }

    /**
     * Set Icinga plugin output.
     *
     * @param string $message Plugin output
     *
     * @return void
     */
    protected function setMessage($message)
    {
        $this->message = (string)$message;
    }

    /**
     * Return Icinga plugin output.
     *
     * @return string
     */
    protected function getMessage()
    {
        return (string)$this->message;
    }

    /**
     * Set Icinga return code.
     *
     * @param int $code Return code
     *
     * @return void
     */
    protected function setCode($code)
    {
        $this->code = (integer)$code;
    }

    /**
     * Return Icinga return code.
     *
     * @return integer
     */
    protected function getCode()
    {
        return (integer)$this->code;
    }
} 
