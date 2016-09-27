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
use SR\Deprecation\DeprecationDefinition;

/**
 * Class DeprecationTest.
 */
class DeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $definition = DeprecationDefinition::create()
            ->deprecate('Usage of this feature has been deprecated in favor of newly introduced API')
            ->createdOn('2016-04-20 19:14 -0400')
            ->createdAt(0, 2, 5)
            ->removalOn('2016-09-26 19:14 -0400')
            ->removalAt(2, 1, 20)
            ->reference(self::class, DeprecationDefinition::class);

        $this->assertNotNull($definition->__toString());
    }

    public function testLogger()
    {
        $logger = $this->getMockLogger();

        $definition = DeprecationDefinition::create($logger)
            ->deprecate('Usage of this feature has been deprecated in favor of newly introduced API')
            ->createdOn('2016-04-20 19:14 -0400')
            ->createdAt(0, 2, 5)
            ->removalOn('2016-09-26 19:14 -0400')
            ->removalAt(2, 1, 20)
            ->reference(self::class, DeprecationDefinition::class);

        $this->assertNotNull($definition->__toString());
        $definition->trigger();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected function getMockLogger()
    {
        return $this
            ->getMockBuilder(LoggerInterface::class)
            ->setMethods(['logDebug'])
            ->getMockForAbstractClass();
    }
}
