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
use SR\Deprecation\Constraint\ConstraintInterface;
use SR\Log\LoggerAwareInterface;

interface DeprecationDefinitionInterface extends LoggerAwareInterface
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @param string $description
     * @param mixed  ...$replacements
     *
     * @return DeprecationDefinitionInterface
     */
    public function describe(string $description, ...$replacements): DeprecationDefinitionInterface;

    /**
     * @param string|\DateTime|ConstraintInterface $constraint
     *
     * @return DeprecationDefinitionInterface
     */
    public function deprecationConstraint($constraint): DeprecationDefinitionInterface;

    /**
     * @param string|\DateTime|ConstraintInterface $constraint
     *
     * @return DeprecationDefinitionInterface
     */
    public function removalConstraint($constraint): DeprecationDefinitionInterface;

    /**
     * @param mixed ...$references
     *
     * @return DeprecationDefinitionInterface
     */
    public function reference(...$references): DeprecationDefinitionInterface;

    /**
     * @param int $level
     *
     * @return DeprecationDefinitionInterface
     */
    public function trigger($level = E_USER_DEPRECATED): DeprecationDefinitionInterface;
}
