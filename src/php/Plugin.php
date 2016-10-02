<?php
namespace randomhost\Icinga;

/**
 * Interface definition for Icinga plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga/
 */
interface Plugin
{
    /**
     * Icinga return code for state "OK".
     *
     * @var int
     */
    const STATE_OK = 0;

    /**
     * Icinga return code for state "WARNING".
     *
     * @var int
     */
    const STATE_WARNING = 1;

    /**
     * Icinga return code for state "CRITICAL".
     *
     * @var int
     */
    const STATE_CRITICAL = 2;

    /**
     * Icinga return code for state "UNKNOWN".
     *
     * @var int
     */
    const STATE_UNKNOWN = 3;

    /**
     * Returns available short options.
     *
     * @return string
     */
    public function getShortOptions();

    /**
     * Returns available long options.
     *
     * @return array
     */
    public function getLongOptions();

    /**
     * Returns the Icinga plugin output.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Returns the Icinga return code.
     *
     * @return integer
     */
    public function getCode();

    /**
     * Runs the Icinga plugin.
     *
     * @return $this
     */
    public function run();
}
