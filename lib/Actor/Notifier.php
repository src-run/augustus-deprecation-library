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

namespace SR\Deprecation\Actor;

use Psr\Log\LoggerInterface;
use SR\Deprecation\Deprecation;
use SR\Deprecation\Exception\DeprecationException;
use SR\Deprecation\Model\Notice;

/**
 * Class Notifier.
 */
class Notifier implements NotifierInterface
{
    /**
     * @var mixed[]
     */
    private $trace;

    /**
     * @var Notice
     */
    private $notice;

    /**
     * {@inheritdoc}
     */
    public function setStack(array $trace) : NotifierInterface
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotice(Notice $notice) : NotifierInterface
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DeprecationException
     */
    public function notify(LoggerInterface $logger = null)
    {
        if ($logger) {
            $logger->debug($this->getMessage());
        }

        if (Deprecation::mode() === Deprecation::USE_DEPRICATION_ERROR) {
            trigger_error($this->getMessage(), E_USER_DEPRECATED);
        }

        if (Deprecation::mode() === Deprecation::USE_EXCEPTION) {
            throw DeprecationException::create($this->getMessage());
        }
    }

    /**
     * @return string
     */
    private function getMessage() : string
    {
        $notice = $this->notice;
        $callContext = $this->getCallingContextFromTrace();

        $date = $notice->hasDate() ? sprintf(' on "%s"', $notice->getDate()->format('Y\-m\-d')) : '';

        $msg = sprintf(
            '%s: "%s::%s" deprecated%s', $notice->getMessage(), $callContext[0], $callContext[1], $date);

        $msgReplacements = '';
        $msgReferences = '';

        if ($notice->hasReplacements()) {
            $msgReplacements = sprintf(
                '(replacements %s)', implode(', ', $notice->getReplacements()));
        }

        if ($notice->hasReferences()) {
            $msgReferences = sprintf(
                '(reference %s)', implode(', ', $notice->getReferences()));
        }

        return sprintf('%s %s %s', $msg, $msgReferences, $msgReplacements);
    }

    /**
     * @return string[]
     */
    private function getCallingContextFromTrace() : array
    {
        $s = array_filter($this->trace, function ($t) {
            return @$t['object'] instanceof \ReflectionMethod;
        });

        $s = array_map(function ($t) { return $t['object']; }, $s);

        $s = array_filter($s, function (\ReflectionMethod $o) {
            $name = $o->getDeclaringClass()->getName();

            return false === strpos($name, 'SR\Deprecation');
        });

        $result = $this->getCallingContextResult(array_values($s));

        return [
            $result->getDeclaringClass()->getName(),
            $result->getName(),
        ];
    }

    /**
     * @param \ReflectionMethod[] $set
     *
     * @return \ReflectionMethod
     */
    private function getCallingContextResult(array $set) : \ReflectionMethod
    {
        return array_shift($set);
    }
}

/* EOF */
