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
    public function __construct($message = null, \DateTime $date = null)
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
    public static function create($message = null, \DateTime $date = null)
    {
        return new static($message, $date);
    }

    /**
     * @return bool
     */
    public function hasReferences()
    {
        return count($this->references) > 0;
    }

    /**
     * @return string[]
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param string $associatedIssue
     *
     * @return Notice
     */
    public function addReference($associatedIssue)
    {
        $this->references[] = $associatedIssue;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasReplacements()
    {
        return count($this->replacements) > 0;
    }

    /**
     * @return string[]
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * @param string $replacement
     *
     * @return Notice
     */
    public function addReplacement($replacement)
    {
        $this->replacements[] = $replacement;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Notice
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDate()
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
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }
}

/* EOF */
