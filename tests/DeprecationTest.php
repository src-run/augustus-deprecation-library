<?php

/*
 * This file is part of the `src-run/augustus-deprecation-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Deprecation\Tests;

use Psr\Log\LoggerInterface;
use SR\Deprecation\Constraint\VersionConstraint;
use SR\Deprecation\DeprecationDefinition;

class DeprecationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public static function dataConstraintsProvider(): array
    {
        return [
            ['2017-01-01', '2018-01-01', '2017-01-01', '2018-01-01'],
            [new \DateTime('2017-01-01'), new \DateTime('2018-01-01'), '2017-01-01', '2018-01-01'],
            ['1', '2', '1', '2'],
            ['4.8', '5.0', '4.8', '5.0'],
            ['2.20.1', '3.0', '2.20.1', '3.0'],
            ['Due to custom constraint', 'on custom constraint', 'Due to custom constraint', 'on custom constraint'],
            [new VersionConstraint(149), new VersionConstraint(150), '149', '150'],
        ];
    }

    /**
     * @param mixed  $deprecationConstraint
     * @param mixed  $removalConstraint
     * @param string $expectedDeprecation
     * @param string $expectedRemoval
     *
     * @dataProvider dataConstraintsProvider
     */
    public function testConstraints($deprecationConstraint, $removalConstraint, string $expectedDeprecation, string $expectedRemoval)
    {
        $definition = (new DeprecationDefinition())
            ->describe('this feature was deprecated in favor of %s', DeprecationTest::class)
            ->deprecationConstraint($deprecationConstraint)
            ->removalConstraint($removalConstraint)
            ->__toString();

        $this->assertContains($expectedDeprecation, $definition);
        $this->assertContains($expectedRemoval, $definition);
    }

    public function testReferences()
    {
        $definition = (new DeprecationDefinition())
            ->describe('Usage of this feature has been deprecated in favor of newly introduced API')
            ->reference(self::class, DeprecationDefinition::class)
            ->__toString();

        $this->assertContains(self::class, $definition);
        $this->assertContains(DeprecationDefinition::class, $definition);
    }

    public function testLoggerIsCalled()
    {
        $definition = (new DeprecationDefinition('1', $logger = $this->getMockLogger()))
            ->describe('Usage of this feature has been deprecated in favor of newly introduced API');

        $logger
            ->expects($this->once())
            ->method('debug')
            ->with($definition->__toString());

        $definition->trigger();
    }

    /**
     * @group legacy
     * @expectedDeprecation In version "1" usage of this feature has been deprecated in favor of SR\Deprecation\Tests\DeprecationTest. [Originated from "SR\Deprecation\Tests\DeprecationTest::testTriggeredDeprecation" in "%sDeprecationTest.php" on line "%d"]
     */
    public function testTriggeredDeprecation()
    {
        (new DeprecationDefinition('1'))
            ->describe('Usage of this feature has been deprecated in favor of %s', self::class)
            ->trigger();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function getMockLogger()
    {
        return $this->getMockBuilder(LoggerInterface::class)->getMockForAbstractClass();
    }
}
