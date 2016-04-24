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

namespace SR\Deprecation\Model;

/**
 * Class Date.
 */
class Date
{
    /**
     * @param string $date
     *
     * @return \DateTime
     */
    public static function create($date) : \DateTime
    {
        return new \DateTime($date);
    }
}

/* EOF */
