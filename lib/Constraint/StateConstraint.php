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

class StateConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $state;

    /**
     * @param string $state
     */
    public function __construct(string $state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getConstrainStringRepresentation(): string
    {
        return $this->__toString();
    }
}
