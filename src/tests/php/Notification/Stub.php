<?php

declare(strict_types=1);

namespace randomhost\Icinga\Tests\Notification;

use randomhost\Icinga\Notification\Base;
use randomhost\Icinga\Notification\Notification;
use randomhost\Icinga\Plugin;

/**
 * Testing stub for randomhost\Icinga\Check\Base.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class Stub extends Base implements Notification
{
    /**
     * Expected exit message.
     *
     * @var string
     */
    private $stubMessage = '';

    /**
     * Expected exit code.
     *
     * @var int
     */
    private $stubCode = Plugin::STATE_UNKNOWN;

    /**
     * Stub constructor.
     *
     * @param string $help      Expected help message text.
     * @param array  $longOpts  Expected long options.
     * @param string $shortOpts Expected short options.
     * @param array  $required  Expected required options.
     * @param string $message   Expected exit message.
     * @param int    $code      Expected exit code.
     */
    public function __construct(
        string $help = '',
        array $longOpts = [],
        string $shortOpts = '',
        array $required = [],
        string $message = '',
        int $code = -1
    ) {
        if ('' !== $help) {
            $this->setHelp($help);
        }
        if (!empty($longOpts)) {
            $this->setLongOptions($longOpts);
        }
        if ('' !== $shortOpts) {
            $this->setShortOptions($shortOpts);
        }
        if (!empty($required)) {
            $this->setRequiredOptions($required);
        }
        if ('' !== $message) {
            $this->stubMessage = $message;
        }
        if (-1 !== $code) {
            $this->stubCode = $code;
        }
    }

    /**
     * Executes the main Icinga notification plugin logic.
     *
     * @return $this
     */
    protected function send(): Plugin
    {
        $this->setMessage($this->stubMessage);
        $this->setCode($this->stubCode);

        return $this;
    }
}