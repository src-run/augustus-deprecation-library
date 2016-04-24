<?php

declare(strict_types=1);

/*
 * This file is part of the `src-run/augustus-deprecation-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Deprecation\Actor;

use Psr\Log\LoggerInterface;
use SR\Deprecation\Model\Notice;

/**
 * Interface NotifierInterface.
 */
interface NotifierInterface
{
    /**
     * @param array $trace
     *
     * @return NotifierInterface
     */
    public function setStack(array $trace) : NotifierInterface;

    /**
     * @param Notice $notice
     *
     * @return NotifierInterface
     */
    public function setNotice(Notice $notice) : NotifierInterface;

    /**
     * @param LoggerInterface|null $logger
     *
     * @return null
     */
    public function notify(LoggerInterface $logger = null);
}

/* EOF */
