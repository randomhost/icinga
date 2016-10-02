<?php
namespace randomhost\Icinga\Notification;

use Exception;
use randomhost\Icinga\Base as IcingaBase;

/**
 * Base class for Icinga notification plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga/
 */
abstract class Base extends IcingaBase implements Notification
{
    /**
     * Sends the Icinga notification.
     *
     * @return $this
     */
    public function run()
    {
        try {
            return $this
                ->preRun()
                ->send();
        } catch (Exception $e) {
            return $this
                ->setMessage($e->getMessage())
                ->setCode($e->getCode());
        }
    }

    /**
     * Executes the main Icinga notification plugin logic.
     *
     * @return $this
     */
    abstract protected function send();
}
