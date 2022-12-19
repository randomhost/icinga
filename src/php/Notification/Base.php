<?php

declare(strict_types=1);

namespace randomhost\Icinga\Notification;

use randomhost\Icinga\Base as IcingaBase;
use randomhost\Icinga\Plugin;

/**
 * Base class for Icinga notification plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
abstract class Base extends IcingaBase implements Notification
{
    /**
     * Command line options available to be used in messages.
     */
    protected const MESSAGE_OPTIONS = [
        'type',
        'service',
        'host',
        'address',
        'state',
        'time',
        'output',
    ];

    /**
     * Sends the Icinga notification.
     *
     * @return $this
     */
    public function run(): Plugin
    {
        try {
            return $this
                ->preRun()
                ->send()
            ;
        } catch (\Exception $e) {
            return $this
                ->setMessage($e->getMessage())
                ->setCode($e->getCode())
            ;
        }
    }

    /**
     * Executes the main Icinga notification plugin logic.
     *
     * @return $this
     */
    abstract protected function send(): Plugin;
}
