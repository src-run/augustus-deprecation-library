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
use SR\Log\LoggerAwareInterface;

interface DeprecationDefinitionInterface extends LoggerAwareInterface
{
    /**
     * @return string
     */
    public function __toString() : string;

    /**
     * @param null|LoggerInterface $logger
     *
     * @return DeprecationDefinitionInterface
     */
    public static function create(LoggerInterface $logger = null) : DeprecationDefinitionInterface;

    /**
     * @param string $description
     *
     * @return DeprecationDefinitionInterface
     */
    public function deprecate(string $description) : DeprecationDefinitionInterface;

    /**
     * @param \DateTime $date
     *
     * @return DeprecationDefinitionInterface
     */
    public function createdOn(string $date, string $format = 'Y-m-d H:i O') : DeprecationDefinitionInterface;

    /**
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     *
     * @return DeprecationDefinitionInterface
     */
    public function createdAt(int $major = null, int $minor = null, $patch = null) : DeprecationDefinitionInterface;

    /**
     * @param string $date
     * @param string $format
     *
     * @return DeprecationDefinitionInterface
     */
    public function removalOn(string $date, string $format = 'Y-m-d H:i O') : DeprecationDefinitionInterface;

    /**
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     *
     * @return DeprecationDefinitionInterface
     */
    public function removalAt(int $major = null, int $minor = null, $patch = null) : DeprecationDefinitionInterface;

    /**
     * @param mixed ...$references
     *
     * @return DeprecationDefinitionInterface
     */
    public function reference(...$references) : DeprecationDefinitionInterface;

    /**
     * @param int $level
     */
    public function trigger($level = E_USER_DEPRECATED);
}
