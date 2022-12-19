<?php

declare(strict_types=1);

namespace randomhost\Icinga;

/**
 * Base class for Icinga plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
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
     * two hyphens (--). For example, a longOptions element "opt" recognizes an
     * option --opt.
     *
     * @var array
     */
    protected $longOptions = ['help'];

    /**
     * Array of option / argument pairs.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Array of required option / argument pairs.
     *
     * @var array
     */
    protected $requiredOptions = [];

    /**
     * Icinga plugin output.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Icinga return code.
     *
     * @var int
     */
    protected $code = self::STATE_UNKNOWN;

    /**
     * Returns available short options.
     */
    public function getShortOptions(): string
    {
        return $this->shortOptions;
    }

    /**
     * Returns available long options.
     */
    public function getLongOptions(): array
    {
        return $this->longOptions;
    }

    /**
     * Returns the Icinga plugin output.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns the Icinga return code.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Sets command line options as returned by getopt().
     *
     * @param array $options Command line options.
     *
     * @return $this
     */
    public function setOptions(array $options): Plugin
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Reads command line options and performs pre-run tasks.
     *
     * @return $this
     */
    protected function preRun(): self
    {
        if (array_key_exists('help', $this->getOptions())) {
            $this->displayHelp();
        }

        $this->checkRequiredParameters();

        return $this;
    }

    /**
     * Checks if all required parameters are set.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException Thrown in case of missing required arguments.
     */
    protected function checkRequiredParameters(): self
    {
        $missing = array_diff(
            $this->getRequiredOptions(),
            array_keys($this->getOptions())
        );
        if (!empty($missing)) {
            throw new \InvalidArgumentException(
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
        throw new \RuntimeException(
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
    protected function setHelp(string $help): self
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Returns the help message for this plugin.
     */
    protected function getHelp(): string
    {
        return $this->help;
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
    protected function setShortOptions(string $options): self
    {
        $this->shortOptions = $options;

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
    protected function setLongOptions(array $options): self
    {
        $this->longOptions = array_merge($this->getLongOptions(), $options);

        return $this;
    }

    /**
     * Return an array of option / argument pairs.
     */
    protected function getOptions(): array
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
    protected function setRequiredOptions(array $options): self
    {
        $this->requiredOptions = $options;

        return $this;
    }

    /**
     * Returns required options.
     */
    protected function getRequiredOptions(): array
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
    protected function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Sets Icinga return code.
     *
     * @param int $code Return code.
     *
     * @return $this
     */
    protected function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }
}
