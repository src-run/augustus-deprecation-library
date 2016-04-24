<?php

declare (strict_types = 1);

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
 * Class Notice.
 */
class Notice
{
    /**
     * @var string
     */
    private $message = '';

    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @var string[]
     */
    private $references = [];

    /**
     * @var string[]
     */
    private $replacements = [];

    /**
     * @param string|null    $message
     * @param \DateTime|null $date
     */
    public function __construct(string $message = null, \DateTime $date = null)
    {
        $this->message = $message;
        $this->date = $date;
    }

    /**
     * @param string|null    $message
     * @param \DateTime|null $date
     *
     * @return Notice
     */
    public static function create(string $message = null, \DateTime $date = null) : Notice
    {
        return new static($message, $date);
    }

    /**
     * @return bool
     */
    public function hasReferences() : bool
    {
        return count($this->references) > 0;
    }

    /**
     * @return string[]
     */
    public function getReferences() : array
    {
        return $this->references;
    }

    /**
     * @param string $associatedIssue
     *
     * @return Notice
     */
    public function addReference(string $associatedIssue) : Notice
    {
        $this->references[] = $associatedIssue;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasReplacements() : bool
    {
        return count($this->replacements) > 0;
    }

    /**
     * @return string[]
     */
    public function getReplacements() : array
    {
        return $this->replacements;
    }

    /**
     * @param string $replacement
     *
     * @return Notice
     */
    public function addReplacement(string $replacement) : Notice
    {
        $this->replacements[] = $replacement;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Notice
     */
    public function setMessage(string $message) : Notice
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDate() : bool
    {
        return $this->date instanceof \DateTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return Notice
     */
    public function setDate(\DateTime $date) : Notice
    {
        $this->date = $date;

        return $this;
    }
}

/* EOF */
