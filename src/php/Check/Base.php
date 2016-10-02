<?php
namespace randomhost\Icinga\Check;

use Exception;
use randomhost\Icinga\Base as IcingaBase;

/**
 * Base class for Icinga check plugins.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga/
 */
abstract class Base extends IcingaBase implements Check
{
    /**
     * Performs the Icinga check.
     *
     * @return $this
     */
    public function run()
    {
        try {
            return $this
                ->preRun()
                ->check();
        } catch (Exception $e) {
            return $this
                ->setMessage($e->getMessage())
                ->setCode($e->getCode());
        }
    }

    /**
     * Executes the main Icinga check plugin logic.
     *
     * @return $this
     */
    abstract protected function check();
}
