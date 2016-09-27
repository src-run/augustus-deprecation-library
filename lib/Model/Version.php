<?php

/*
 * This file is part of the `src-run/augustus-deprecation-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Deprecation\Model;

class Version
{
    /**
     * @var string
     */
    private $major;

    /**
     * @var string
     */
    private $minor;

    /**
     * @var string
     */
    private $patch;

    /**
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     */
    public function __construct(int $major = null, int $minor = null, int $patch = null)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return sprintf('v%d.%d.%d', $this->getMajor(), $this->getMinor(), $this->getPatch());
    }

    /**
     * @return string
     */
    public function getVersion() : string
    {
        return $this->__toString();
    }

    /**
     * @return int
     */
    public function getMajor() : int
    {
        return $this->major ?: 0;
    }

    /**
     * @return int
     */
    public function getMinor() : int
    {
        return $this->minor ?: 0;
    }

    /**
     * @return int
     */
    public function getPatch() : int
    {
        return $this->patch ?: 0;
    }
}
