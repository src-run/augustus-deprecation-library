<?php

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
    private $backtrace;

    /**
     * @var Notice
     */
    private $notice;

    /**
     * {@inheritdoc}
     */
    public function setBacktrace(array $backtrace)
    {
        $this->backtrace = $backtrace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotice(Notice $notice)
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
    private function getMessage()
    {
        $method = $this->getBacktraceExternallyInvokedMethodFirst();

        $message = sprintf(
            '%s: "%s::%s" deprecated on %s',
            $this->notice->getMessage(),
            $method->getDeclaringClass()->getName(),
            $method->getName(),
            ($date = $this->notice->getDate()) ? $date->format('r') : 'unspecified date');

        return sprintf(
            '%s %s %s',
            $message,
            $this->getMessageReferences(),
            $this->getMessageReplacements());
    }

    /**
     * @return string
     */
    private function getMessageReplacements()
    {
        $template = '(replacements: %s)';

        if (!$this->notice->hasReplacements()) {
            return sprintf($template, 'none');
        }

        return sprintf($template,
            implode(', ', $this->notice->getReplacements()));
    }

    /**
     * @return string
     */
    private function getMessageReferences()
    {
        $template = '(references: %s)';

        if (!$this->notice->hasReferences()) {
            return sprintf($template, 'none');
        }

        return sprintf($template, implode(', ', $this->notice->getReferences()));
    }

    /**
     * @return \ReflectionMethod
     */
    protected function getBacktraceExternallyInvokedMethodFirst()
    {
        $methodCollection = $this->getBacktraceExternallyInvokedMethods();
        
        return array_shift($methodCollection);
    }

    /**
     * @return \ReflectionMethod[]|
     */
    private function getBacktraceExternallyInvokedMethods()
    {
        $methods = [];
        foreach ($this->backtrace as $step) {
            if (isset($step['object']) && $step['object'] instanceof \ReflectionMethod) {
                $methods[] = $step['object'];
            }
        }

        return array_filter($methods, function (\ReflectionMethod $m) {
            return false === strpos($m->getDeclaringClass()->getName(), 'SR\Deprecation');
        });
    }
}

/* EOF */
