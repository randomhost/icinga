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
use randomhost\Icinga\Base as IcingaBase;

/**
 * Base class for Icinga check plugins
 *
 * @category  Monitoring
 * @package   PHP_Icinga
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
abstract class Base extends IcingaBase implements Check
{
    /**
     * Performs the Icinga check.
     *
     * @return void
     */
    public function run()
    {
        $this->preRun();
        $this->check();
        $this->postRun();
    }

    /**
     * Must be implemented by all child classes and contains the main
     * Icinga check plugin logic.
     *
     * @return void
     */
    protected abstract function check();
} 
