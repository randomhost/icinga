<?php
namespace randomhost\Icinga;

use InvalidArgumentException;
use RuntimeException;

/**
 * Base class for Icinga plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga/
 */
abstract class Base implements Plugin
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
     * Array of option / argument pairs.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Array of required option / argument pairs.
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
    protected $code = self::STATE_UNKNOWN;

    /**
     * Returns available short options.
     *
     * @return string
     */
    public function getShortOptions()
    {
        return (string)$this->shortOptions;
    }

    /**
     * Returns available long options.
     *
     * @return array
     */
    public function getLongOptions()
    {
        return $this->longOptions;
    }

    /**
     * Returns the Icinga plugin output.
     *
     * @return string
     */
    public function getMessage()
    {
        return (string)$this->message;
    }

    /**
     * Returns the Icinga return code.
     *
     * @return integer
     */
    public function getCode()
    {
        return (integer)$this->code;
    }

    /**
     * Sets command line options as returned by getopt().
     *
     * @param array $options Command line options.
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Reads command line options and performs pre-run tasks.
     *
     * @return $this
     */
    protected function preRun()
    {
        if (array_key_exists('help', $this->getOptions())) {
            $this->displayHelp();
        } // @codeCoverageIgnore

        $this->checkRequiredParameters();

        return $this;
    }

    /**
     * Checks if all required parameters are set.
     *
     * @return $this
     *
     * @throws InvalidArgumentException Thrown in case of missing required arguments.
     */
    protected function checkRequiredParameters()
    {
        $missing = array_diff(
            $this->getRequiredOptions(),
            array_keys($this->getOptions())
        );
        if (!empty($missing)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Missing required parameters: %s',
                    implode(', ', $missing)
                ),
                self::STATE_UNKNOWN
            );
        }

        return $this;
    }

    /**
     * Displays a help message.
     */
    protected function displayHelp()
    {
        throw new RuntimeException(
            $this->getHelp(),
            self::STATE_UNKNOWN
        );
    }

    /**
     * Sets the help message for this plugin.
     *
     * @param string $help Help message text.
     *
     * @return $this
     */
    protected function setHelp($help)
    {
        $this->help = (string)$help;

        return $this;
    }

    /**
     * Returns the help message for this plugin.
     *
     * @return string
     */
    protected function getHelp()
    {
        return (string)$this->help;
    }

    /**
     * Sets short options.
     *
     * Each character in this string will be used as option characters.
     * Only a-z, A-Z and 0-9 are allowed.
     *
     * @param string $options Option characters.
     *
     * @return $this
     */
    protected function setShortOptions($options)
    {
        $this->shortOptions = (string)$options;

        return $this;
    }

    /**
     * Sets long options.
     *
     * Each element in this array will be used as option strings.
     *
     * @param array $options Array with option strings.
     *
     * @return $this
     */
    protected function setLongOptions(array $options)
    {
        $this->longOptions = array_merge($this->getLongOptions(), $options);

        return $this;
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
     * Sets required options.
     *
     * @param array $options Array with option strings.
     *
     * @return $this
     */
    protected function setRequiredOptions(array $options)
    {
        $this->requiredOptions = $options;

        return $this;
    }

    /**
     * Returns required options.
     *
     * @return array
     */
    protected function getRequiredOptions()
    {
        return $this->requiredOptions;
    }

    /**
     * Sets Icinga plugin output.
     *
     * @param string $message Plugin output.
     *
     * @return $this
     */
    protected function setMessage($message)
    {
        $this->message = (string)$message;

        return $this;
    }

    /**
     * Sets Icinga return code.
     *
     * @param int $code Return code.
     *
     * @return $this
     */
    protected function setCode($code)
    {
        $this->code = (integer)$code;

        return $this;
    }
}
