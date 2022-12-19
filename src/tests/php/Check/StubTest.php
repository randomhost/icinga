<?php

declare(strict_types=1);

namespace randomhost\Icinga\Tests\Check;

use PHPUnit\Framework\TestCase;
use randomhost\Icinga\Plugin;

/**
 * Unit test for Base.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class StubTest extends TestCase
{
    public function testRunWithoutParameters()
    {
        $stub = new Stub();

        $this->assertSame(
            $stub,
            $stub->run()
        );

        $this->assertEquals(
            Plugin::STATE_UNKNOWN,
            $stub->getCode()
        );

        $this->assertEquals(
            '',
            $stub->getMessage()
        );
    }

    public function testRunWithHelpParameter()
    {
        $helpText = 'sample help output';

        $stub = new Stub($helpText);

        $options = [
            'help' => '',
        ];

        $longOpts = array_keys($options);

        $this->assertSame(
            $stub,
            $stub->setOptions(
                $options
            )
        );

        $this->assertSame(
            $stub,
            $stub->run()
        );

        $this->assertEmpty($stub->getShortOptions());

        $this->assertEquals(
            $longOpts,
            $stub->getLongOptions()
        );

        $this->assertEquals(
            Plugin::STATE_UNKNOWN,
            $stub->getCode()
        );

        $this->assertEquals(
            $helpText,
            $stub->getMessage()
        );
    }

    public function testRunWithLongOptions()
    {
        $statusMessage = 'test with long options';

        $options = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $longOpts = array_keys($options);

        $stub = new Stub(
            '',
            $longOpts,
            '',
            [],
            $statusMessage,
            Plugin::STATE_UNKNOWN
        );

        $this->assertSame(
            array_merge(
                ['help'],
                $longOpts
            ),
            $stub->getLongOptions()
        );

        $this->assertSame(
            $stub,
            $stub->setOptions(
                $options
            )
        );

        $this->assertSame(
            $stub,
            $stub->run()
        );

        $this->assertEquals(
            Plugin::STATE_UNKNOWN,
            $stub->getCode()
        );

        $this->assertEquals(
            $statusMessage,
            $stub->getMessage()
        );
    }

    public function testRunWithShortOptions()
    {
        $statusMessage = 'test with short options';

        $options = [
            'a' => '1',
            'b' => '2',
        ];

        $shortOpts = implode('', array_keys($options));

        $stub = new Stub(
            '',
            [],
            $shortOpts,
            [],
            $statusMessage,
            Plugin::STATE_UNKNOWN
        );

        $this->assertSame(
            $shortOpts,
            $stub->getShortOptions()
        );

        $this->assertSame(
            $stub,
            $stub->setOptions(
                $options
            )
        );

        $this->assertSame(
            $stub,
            $stub->run()
        );

        $this->assertEquals(
            Plugin::STATE_UNKNOWN,
            $stub->getCode()
        );

        $this->assertEquals(
            $statusMessage,
            $stub->getMessage()
        );
    }

    public function testRunWithRequiredOptionsSet()
    {
        $statusMessage = 'test with required options';

        $options = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $longOpts = array_keys($options);

        $stub = new Stub(
            '',
            $longOpts,
            '',
            $longOpts,
            $statusMessage,
            Plugin::STATE_UNKNOWN
        );

        $this->assertSame(
            array_merge(
                ['help'],
                $longOpts
            ),
            $stub->getLongOptions()
        );

        $this->assertSame(
            $stub,
            $stub->setOptions(
                $options
            )
        );

        $this->assertSame(
            $stub,
            $stub->run()
        );

        $this->assertEquals(
            Plugin::STATE_UNKNOWN,
            $stub->getCode()
        );

        $this->assertEquals(
            $statusMessage,
            $stub->getMessage()
        );
    }

    public function testRunWithRequiredOptionsMissing()
    {
        $statusMessage = 'test with missing required options';

        $options = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $longOpts = array_keys($options);

        $stub = new Stub(
            '',
            $longOpts,
            '',
            $longOpts,
            $statusMessage,
            Plugin::STATE_UNKNOWN
        );

        $this->assertSame(
            array_merge(
                ['help'],
                $longOpts
            ),
            $stub->getLongOptions()
        );

        unset($options['param1']);
        $this->assertSame(
            $stub,
            $stub->setOptions(
                $options
            )
        );

        $this->assertSame(
            $stub,
            $stub->run()
        );

        $this->assertEquals(
            Plugin::STATE_UNKNOWN,
            $stub->getCode()
        );

        $this->assertEquals(
            'Missing required parameters: param1',
            $stub->getMessage()
        );
    }
}
