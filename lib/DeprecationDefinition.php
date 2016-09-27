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
use SR\Deprecation\Model\Version;
use SR\Log\LoggerAwareTrait;
use SR\Util\Context\FileContext;

class DeprecationDefinition implements DeprecationDefinitionInterface
{
    use LoggerAwareTrait;

    /**
     * @var FileContext
     */
    private $context;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $createdOnDate;

    /**
     * @var Version
     */
    private $createdAtVersion;

    /**
     * @var \DateTime
     */
    private $removalOnDate;

    /**
     * @var Version
     */
    private $removalAtVersion;

    /**
     * @var string[]
     */
    private $references;

    /**
     * @param null|LoggerInterface $logger
     * @param bool                 $inferCallingContext
     */
    public function __construct(LoggerInterface $logger = null, bool $inferCallingContext = true)
    {
        if ($logger) {
            $this->setLogger($logger);
        }

        if ($inferCallingContext) {
            $this->inferCallingContextFromTrace(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10));
        }
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->compileMessage();
    }

    /**
     * @param null|LoggerInterface $logger
     *
     * @return DeprecationDefinitionInterface
     */
    public static function create(LoggerInterface $logger = null) : DeprecationDefinitionInterface
    {
        return new static($logger);
    }

    /**
     * @param string $description
     *
     * @return DeprecationDefinitionInterface
     */
    public function deprecate(string $description) : DeprecationDefinitionInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return DeprecationDefinitionInterface
     */
    public function createdOn(string $date, string $format = 'Y-m-d H:i O') : DeprecationDefinitionInterface
    {
        $this->createdOnDate = \DateTime::createFromFormat($format, $date);

        return $this;
    }

    /**
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     *
     * @return DeprecationDefinitionInterface
     */
    public function createdAt(int $major = null, int $minor = null, $patch = null) : DeprecationDefinitionInterface
    {
        $this->createdAtVersion = new Version($major, $minor, $patch);

        return $this;
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return DeprecationDefinitionInterface
     */
    public function removalOn(string $date, string $format = 'Y-m-d H:i O') : DeprecationDefinitionInterface
    {
        $this->removalOnDate = \DateTime::createFromFormat($format, $date);

        return $this;
    }

    /**
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     *
     * @return DeprecationDefinitionInterface
     */
    public function removalAt(int $major = null, int $minor = null, $patch = null) : DeprecationDefinitionInterface
    {
        $this->removalAtVersion = new Version($major, $minor, $patch);

        return $this;
    }

    /**
     * @param mixed ...$references
     *
     * @return DeprecationDefinitionInterface
     */
    public function reference(...$references) : DeprecationDefinitionInterface
    {
        $this->references = $references;

        return $this;
    }

    /**
     * @param int $level
     */
    public function trigger($level = E_USER_DEPRECATED)
    {
        $message = $this->compileMessage();

        $this->logDebug($message);
        @trigger_error($message, $level);
    }

    /**
     * @param array $trace
     */
    private function inferCallingContextFromTrace(array $trace = [])
    {
        $trace = array_filter($trace, function (array $t) {
            return isset($t['file']) && $t['file'] !== __FILE__;
        });

        $context = array_shift($trace);

        $this->context = new FileContext($context['file'], $context['line']);
    }

    /**
     * @return string
     */
    private function compileMessage()
    {
        return vsprintf('NOTICE: %s [created%s] [removal%s] (%s from "%s:%d")', [
            $this->description,
            $this->getDateVersionString($this->createdOnDate, $this->createdAtVersion) ?: 'null',
            $this->getDateVersionString($this->removalOnDate, $this->removalAtVersion) ?: 'null',
            $this->context->getMethodName(true),
            $this->context->getFilePathname(),
            $this->context->getLine(),
        ]);
    }

    /**
     * @param \DateTime|null $date
     * @param Version|null   $version
     *
     * @return string
     */
    private function getDateVersionString(\DateTime $date = null, Version $version = null)
    {
        $return = '';

        if ($date) {
            $return .= ':'.$date->format('Y.m.d');
        }

        if ($version) {
            $return .= ':'.$version->getVersion();
        }

        return empty($return) ? null : $return;
    }
}
