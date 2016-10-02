<?php
namespace randomhost\Icinga\Check;

use randomhost\Icinga\Plugin;

/**
 * Testing stub for randomhost\Icinga\Check\Base.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga/
 */
class Stub extends Base implements Check
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
        $help = '',
        $longOpts = array(),
        $shortOpts = '',
        $required = array(),
        $message = '',
        $code = -1
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
     * Executes the main Icinga check plugin logic.
     *
     * @return $this
     */
    protected function check()
    {
        $this->setMessage($this->stubMessage);
        $this->setCode($this->stubCode);

        return $this;
    }
}
