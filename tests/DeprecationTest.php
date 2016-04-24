<?php

/*
 * This file is part of the `src-run/augustus-deprecation-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Deprecation;

use SR\Deprecation\Actor\Notifier;
use SR\Deprecation\Deprecation;
use SR\Deprecation\Model\Date;
use SR\Deprecation\Model\Notice;

/**
 * Class DeprecationTest.
 */
class DeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testDeprecationsStateDefault()
    {
        $d = new Deprecation();
        $p = (new \ReflectionObject($d))->getProperty('enabled');
        $p->setAccessible(true);

        $this->assertNotTrue($p->getValue($d));
    }

    public function testDeprecationStateCanBeEnabledAndDisabled()
    {
        $d = new Deprecation();
        $p = (new \ReflectionObject($d))->getProperty('enabled');
        $p->setAccessible(true);

        Deprecation::enable();
        $this->assertTrue($p->getValue($d));
    }
    
    public function testDeprecationDefinitionResolvedCallingContext()
    {
        $n = new Notifier();
        Deprecation::enable(null, $n);
        Deprecation::definition(
            Notice::create('Deprecation message text', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getCallingContextFromTrace');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertSame([__CLASS__, __FUNCTION__], $string);
    }

    public function testDeprecationDefinitionResolvedMessage()
    {
        $n = new Notifier();
        Deprecation::enable(null, $n);
        Deprecation::definition(
            Notice::create('Deprecation message text', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getMessage');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertRegExp('{Deprecation message text:.*}', $string);

        $n = new Notifier();
        Deprecation::enable(null, $n);
        Deprecation::definition(
            Notice::create()
                ->setMessage('Deprecation message text 2')
                ->setDate(Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getMessage');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertRegExp('{Deprecation message text 2:.*}', $string);
    }

    public function testInvokeWhenDisabled()
    {
        $d = new Deprecation();
        $p = (new \ReflectionObject($d))->getProperty('notifier');
        $p->setAccessible(true);
        $p->setValue($d, $n = new Notifier());
        $p = (new \ReflectionObject($d))->getProperty('enabled');
        $p->setAccessible(true);
        $p->setValue($d, false);

        Deprecation::invoke(
            Notice::create('Deprecation message text', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getMessage');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertRegExp('{Deprecation message text:.*}', $string);
    }

    public function testInvokeWhenEnabled()
    {
        $this->expectException('\PHPUnit_Framework_Error');
        $this->expectExceptionMessageRegExp('{Deprecation message text:.*'.__FUNCTION__.'\)}');

        Deprecation::enable();
        Deprecation::invoke(
            Notice::create('Deprecation message text', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );
    }

    public function testInvokeWithLogger()
    {
        $this->expectException('\PHPUnit_Framework_Error');
        $this->expectExceptionMessageRegExp('{Another deprecation message:.*}');

        $logger = $this
            ->getMockBuilder('\Psr\Log\AbstractLogger')
            ->setMethods(['debug'])
            ->getMockForAbstractClass();
        
        $logger
            ->expects($this->once())
            ->method('debug')
            ->withAnyParameters();

        Deprecation::enable($logger);
        Deprecation::invoke(Notice::create('Another deprecation message', Date::create('2020-01-01')));
    }

    public function testInvokeNoNativeErrorWithException()
    {
        $this->expectException('\SR\Deprecation\Exception\DeprecationException');
        $this->expectExceptionMessageRegExp('{Exception deprecation message:.*'.__FUNCTION__.'\)}');

        Deprecation::enable();
        Deprecation::mode(Deprecation::USE_EXCEPTION);
        Deprecation::invoke(
            Notice::create('Exception deprecation message', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );
    }

    public function testInvokeNoNativeErrorWithNoException()
    {
        $n = new Notifier();
        Deprecation::enable(null, $n);
        Deprecation::mode('invalid_mode_string');
        Deprecation::invoke(
            Notice::create('Invalid mode deprecation message', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getMessage');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertRegExp('{Invalid mode deprecation message:.*}', $string);
    }
}

/* EOF */
