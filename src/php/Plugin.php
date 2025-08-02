<?php

declare(strict_types=1);

namespace randomhost\Icinga;

/**
 * Interface definition for Icinga plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2025 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
interface Plugin
{
    /**
     * Icinga return code for state "OK".
     *
     * @var int
     */
    public const STATE_OK = 0;

    /**
     * Icinga return code for state "WARNING".
     *
     * @var int
     */
    public const STATE_WARNING = 1;

    /**
     * Icinga return code for state "CRITICAL".
     *
     * @var int
     */
    public const STATE_CRITICAL = 2;

    /**
     * Icinga return code for state "UNKNOWN".
     *
     * @var int
     */
    public const STATE_UNKNOWN = 3;

    /**
     * Returns available short options.
     */
    public function getShortOptions(): string;

    /**
     * Returns available long options.
     */
    public function getLongOptions(): array;

    /**
     * Returns the Icinga plugin output.
     */
    public function getMessage(): string;

    /**
     * Returns the Icinga return code.
     */
    public function getCode(): int;

    /**
     * Runs the Icinga plugin.
     *
     * @return $this
     */
    public function run(): self;
}
