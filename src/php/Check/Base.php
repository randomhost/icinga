<?php

declare(strict_types=1);

namespace randomhost\Icinga\Check;

use randomhost\Icinga\Base as IcingaBase;
use randomhost\Icinga\Plugin;

/**
 * Base class for Icinga check plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2025 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
abstract class Base extends IcingaBase implements Check
{
    /**
     * Performs the Icinga check.
     *
     * @return $this
     */
    public function run(): Plugin
    {
        try {
            return $this
                ->preRun()
                ->check()
            ;
        } catch (\Exception $e) {
            return $this
                ->setMessage($e->getMessage())
                ->setCode($e->getCode())
            ;
        }
    }

    /**
     * Executes the main Icinga check plugin logic.
     *
     * @return $this
     */
    abstract protected function check(): Plugin;
}
