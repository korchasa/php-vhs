<?php declare(strict_types=1);

namespace korchasa\Vhs;

use korchasa\matched\Match;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class BodyConstraint extends Constraint
{
    /**
     * @var string
     */
    protected $pattern;

    private $isJson;

    /**
     * @param string $pattern
     * @param bool $isJson
     */
    public function __construct($pattern, bool $isJson)
    {
        parent::__construct();
        $this->pattern = $pattern;
        $this->isJson = $isJson;
    }

    /**
     * @param mixed $other
     * @param string $description
     * @param boolean $returnResult
     *
     * @return boolean
     * @throws \Exception
     */
    public function evaluate(
        $other,
        $description = 'Failed asserting that json matched pattern',
        $returnResult = false
    ) :bool {
        if ($this->isJson) {
            $patternString = json_encode($this->pattern);
            return Match::json($patternString, $other, Match::ANY_SYMBOL, function ($expected, $actual, $message) {
                $diffBuilder = new UnifiedDiffOutputBuilder("--- Pattern\n+++ Actual\n");
                $diff = (new Differ($diffBuilder))->diff(var_export($expected, true), var_export($actual, true));
                throw new ExpectationFailedException($message."\n".$diff);
            });
        }

        return Match::string($this->pattern, $other, Match::ANY_SYMBOL, function ($expected, $actual, $message) {
            $diffBuilder = new UnifiedDiffOutputBuilder("--- Pattern\n+++ Actual\n");
            $diff = (new Differ($diffBuilder))->diff($expected, $actual);
            throw new ExpectationFailedException($message."\n".$diff);
        });
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            'matches JSON string "%s"',
            $this->pattern
        );
    }
}
