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
use SR\Deprecation\Actor\NotifierInterface;
use SR\Deprecation\Model\Notice;

/**
 * Interface DeprecationInterface.
 */
interface DeprecationInterface
{
    /**
     * @param null|LoggerInterface   $logger
     * @param null|NotifierInterface $notifier
     */
    public static function enable(LoggerInterface $logger = null, NotifierInterface $notifier = null);

    /**
     * @param string|null $mode
     *
     * @return string
     */
    public static function mode($mode = null);

    /**
     * @param Notice $notice
     */
    public static function definition(Notice $notice);

    /**
     * @param Notice|null $notice
     */
    public static function invoke(Notice $notice = null);
}

/* EOF */
