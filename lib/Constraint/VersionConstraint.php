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

class VersionConstraint implements ConstraintInterface
{
    /**
     * @var int
     */
    private $major;

    /**
     * @var int|null
     */
    private $minor;

    /**
     * @var int|null
     */
    private $patch;

    /**
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     */
    public function __construct(int $major, int $minor = null, int $patch = null)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $version = $this->major;

        if (null !== $this->minor) {
            $version .= sprintf('.%d', $this->minor);

            if (null !== $this->patch) {
                $version .= sprintf('.%d', $this->patch);
            }
        }

        return $version;
    }

    /**
     * @return string
     */
    public function getConstrainStringRepresentation(): string
    {
        return sprintf('in version "%s"', $this->__toString());
    }
}
