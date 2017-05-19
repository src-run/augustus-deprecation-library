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
use SR\Deprecation\Constraint\DateConstraint;
use SR\Deprecation\Constraint\NullConstraint;
use SR\Deprecation\Constraint\StateConstraint;
use SR\Deprecation\Constraint\VersionConstraint;
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
     * @var string[]
     */
    private $references;

    /**
     * @var ConstraintInterface
     */
    private $deprecationConstraint;

    /**
     * @var ConstraintInterface
     */
    private $removalConstraint;

    /**
     * @param string|\DateTime|ConstraintInterface|null
     * @param null|LoggerInterface $logger
     */
    public function __construct($deprecationConstraint = null, LoggerInterface $logger = null)
    {
        $this->deprecationConstraint($deprecationConstraint);
        $this->setLogger($logger);
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->compileMessage();
    }

    /**
     * @param string $description
     * @param mixed  ...$replacements
     *
     * @return DeprecationDefinitionInterface
     */
    public function describe(string $description, ...$replacements): DeprecationDefinitionInterface
    {
        $this->description = 0 === count($replacements) ? $description : vsprintf($description, $replacements);

        return $this;
    }

    /**
     * @param string|\DateTime|ConstraintInterface $constraint
     *
     * @return DeprecationDefinitionInterface
     */
    public function deprecationConstraint($constraint): DeprecationDefinitionInterface
    {
        $this->deprecationConstraint = $this->getConstraintInstance($constraint);

        return $this;
    }

    /**
     * @param string|\DateTime|ConstraintInterface $constraint
     *
     * @return DeprecationDefinitionInterface
     */
    public function removalConstraint($constraint): DeprecationDefinitionInterface
    {
        $this->removalConstraint = $this->getConstraintInstance($constraint);

        return $this;
    }

    /**
     * @param mixed ...$references
     *
     * @return DeprecationDefinitionInterface
     */
    public function reference(...$references): DeprecationDefinitionInterface
    {
        $this->references = $references;

        return $this;
    }

    /**
     * @param int $level
     *
     * @return DeprecationDefinitionInterface
     */
    public function trigger($level = E_USER_DEPRECATED): DeprecationDefinitionInterface
    {
        $this->logDebug($message = $this->compileMessage());
        @trigger_error($message, $level);

        return $this;
    }

    /**
     * @return FileContext
     */
    private function getContext(): FileContext
    {
        if (!$this->context) {
            $this->setupContext();
        }

        return $this->context;
    }

    /**
     * @return self
     */
    private function setupContext(): self
    {
        $trace = array_filter(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10), function (array $t) {
            return isset($t['file']) && $t['file'] !== __FILE__;
        });

        $context = array_shift($trace);

        $this->context = new FileContext($context['file'], $context['line']);

        return $this;
    }

    /**
     * @return string
     */
    private function compileMessage(): string
    {
        $message = '';

        if ($this->deprecationConstraint) {
            $message = ucfirst($this->deprecationConstraint->getConstrainStringRepresentation()) . ' ';
        }

        $message .= lcfirst($this->description);

        if ($this->removalConstraint) {
            $message .= sprintf(', and will be removed %s', $this->removalConstraint->getConstrainStringRepresentation());
        }

        if ($this->references) {
            $message .= sprintf('. Use "%s" instead', implode(':', $this->references));
        }

        $message .= vsprintf('. [Originated from "%s" in "%s" on line "%d"]', [
            $this->getContext()->getMethodName(true),
            $this->getContext()->getFilePathname(),
            $this->getContext()->getLine(),
        ]);

        return trim($message);
    }

    /**
     * @param string|\DateTime|ConstraintInterface $constraint
     *
     * @return ConstraintInterface
     */
    private function getConstraintInstance($constraint): ConstraintInterface
    {
        if (null === $constraint) {
            return new NullConstraint();
        }

        if ($constraint instanceof ConstraintInterface) {
            return $constraint;
        }

        if ($constraint instanceof \DateTime) {
            return new DateConstraint($constraint);
        }

        if (1 === preg_match('{^(?<y>[0-9]{4})-(?<m>[0-9]{2})-(?<d>[0-9]{2})$}', $constraint)) {
            return new DateConstraint(new \DateTime($constraint));
        }

        if (1 === preg_match('{^(?<major>[0-9]{1,})(.(?<minor>[0-9]{1,}))?(.(?<patch>[0-9]{1,}))?$}', $constraint, $matches)) {
            return new VersionConstraint($matches['major'], $matches['minor'] ?? null, $matches['patch'] ?? null);
        }

        return new StateConstraint($constraint);
    }
}
