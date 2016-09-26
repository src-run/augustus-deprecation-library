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
use SR\Deprecation\Deprecate;
use SR\Deprecation\Model\Date;
use SR\Deprecation\Model\Notice;

/**
 * Class DeprecationTest.
 */
class DeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testDeprecationsStateDefault()
    {
        $d = new Deprecate();
        $p = (new \ReflectionObject($d))->getProperty('enabled');
        $p->setAccessible(true);

        $this->assertNotTrue($p->getValue($d));
    }

    public function testDeprecationStateCanBeEnabledAndDisabled()
    {
        $d = new Deprecate();
        $p = (new \ReflectionObject($d))->getProperty('enabled');
        $p->setAccessible(true);

        Deprecate::enable();
        $this->assertTrue($p->getValue($d));
    }

    public function testDeprecationBacktraceFirstExternalInvokedMethod()
    {
        $n = new Notifier();
        Deprecate::enable(null, $n);
        Deprecate::define(
            Notice::create('Deprecation message text', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getBacktraceExternallyInvokedMethodFirst');
        $m->setAccessible(true);

        $method = $m->invoke($n);

        $this->assertSame(__CLASS__, $method->getDeclaringClass()->getName());
        $this->assertSame(__FUNCTION__, $method->getName());
    }

    public function testDeprecationDefinitionResolvedMessage()
    {
        $n = new Notifier();
        Deprecate::enable(null, $n);
        Deprecate::define(
            Notice::create('Deprecation message text', Date::create('2020-01-01'))
        );

        $m = (new \ReflectionObject($n))->getMethod('getMessage');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertRegExp('{Deprecation message text:.*}', $string);

        $n = new Notifier();
        Deprecate::enable(null, $n);
        Deprecate::define(
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
        $this->assertRegExp('{#1234}', $string);
        $this->assertRegExp('{#98}', $string);
        $this->assertRegExp('{2020}', $string);
        $this->assertRegExp('{'.preg_quote(__METHOD__).'}', $string);

        $n = new Notifier();
        Deprecate::enable(null, $n);
        Deprecate::define(
            Notice::create()
                ->setMessage('Deprecation message text 2')
                ->setDate(Date::create('2020-01-01'))
                ->setReferences('#1234', '#98')
                ->setReplacements(__METHOD__, __FUNCTION__, __CLASS__)
        );

        $m = (new \ReflectionObject($n))->getMethod('getMessage');
        $m->setAccessible(true);

        $string = $m->invoke($n);

        $this->assertRegExp('{Deprecation message text 2:.*}', $string);
        $this->assertRegExp('{#1234}', $string);
        $this->assertRegExp('{#98}', $string);
        $this->assertRegExp('{2020}', $string);
        $this->assertRegExp('{'.preg_quote(__METHOD__).'}', $string);
        $this->assertRegExp('{'.preg_quote(__FUNCTION__).'}', $string);
        $this->assertRegExp('{'.preg_quote(__CLASS__).'}', $string);
    }

    public function testInvokeWhenDisabled()
    {
        $d = new Deprecate();
        $p = (new \ReflectionObject($d))->getProperty('notifier');
        $p->setAccessible(true);
        $p->setValue($d, $n = new Notifier());
        $p = (new \ReflectionObject($d))->getProperty('enabled');
        $p->setAccessible(true);
        $p->setValue($d, false);

        Deprecate::invoke(
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

        Deprecate::enable();
        Deprecate::invoke(
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

        Deprecate::enable($logger);
        Deprecate::invoke(Notice::create('Another deprecation message', Date::create('2020-01-01')));
    }

    public function testInvokeNoNativeErrorWithException()
    {
        $this->expectException('\SR\Deprecation\Exception\DeprecationException');
        $this->expectExceptionMessageRegExp('{Exception deprecation message:.*'.__FUNCTION__.'\)}');

        Deprecate::enable();
        Deprecate::mode(Deprecate::USE_THROWN_EXCEPTION);
        Deprecate::invoke(
            Notice::create('Exception deprecation message', Date::create('2020-01-01'))
                ->addReference('#1234')
                ->addReference('#98')
                ->addReplacement(__METHOD__)
        );
    }

    public function testInvokeNoNativeErrorWithNoException()
    {
        $n = new Notifier();
        Deprecate::enable(null, $n);
        Deprecate::mode('invalid_mode_string');
        Deprecate::invoke(
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
