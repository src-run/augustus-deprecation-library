<?php

/*
 * This file is part of the `src-run/augustus-deprecation-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Deprecation\Constraint;

class DateConstraint implements ConstraintInterface
{
    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $format;

    /**
     * @param \DateTime $dateTime
     * @param string    $format
     */
    public function __construct(\DateTime $dateTime, string $format = 'Y-m-d')
    {
        $this->dateTime = $dateTime;
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->dateTime->format($this->format);
    }

    /**
     * @return string
     */
    public function getConstrainStringRepresentation(): string
    {
        return sprintf('on "%s"', $this->__toString());
    }
}
