<?php

/*
 * This file is part of the `src-run/augustus-deprecation-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Deprecation;

use Psr\Log\LoggerInterface;
use SR\Deprecation\Model\Notice;
use SR\Deprecation\Actor\Notifier;
use SR\Deprecation\Actor\NotifierInterface;

class Deprecation implements DeprecationInterface
{
    /**
     * @var string
     */
    const USE_DEPRICATION_ERROR = 'deprecation_error';

    /**
     * @var string
     */
    const USE_EXCEPTION = 'throw_exception';

    /**
     * @var bool
     */
    private static $enabled = false;

    /**
     * @var string
     */
    private static $mode = self::USE_DEPRICATION_ERROR;

    /**
     * @var null|LoggerInterface
     */
    private static $logger;

    /**
     * @var null|NotifierInterface
     */
    private static $notifier;

    /**
     * {@inheritdoc}
     */
    public static function enable(LoggerInterface $logger = null, NotifierInterface $notifier = null)
    {
        static::$enabled = true;
        static::$logger = $logger;
        static::$notifier = $notifier;

        if (!static::$notifier) {
            static::$notifier = new Notifier();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function mode($mode = null)
    {
        if ($mode !== null) {
            static::$mode = $mode;
        }

        return static::$mode;
    }

    /**
     * {@inheritdoc}
     */
    public static function definition(Notice $notice)
    {
        $stack = debug_backtrace(
            DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        static::$notifier
            ->setBacktrace($stack)
            ->setNotice($notice);
    }

    /**
     * {@inheritdoc}
     */
    public static function invoke(Notice $notice = null)
    {
        if ($notice !== null) {
            static::definition($notice);
        }

        if (static::$enabled) {
            static::$notifier->notify(static::$logger);
        }
    }
}

/* EOF */
