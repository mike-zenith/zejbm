<?php

declare(strict_types=1);

namespace zejbm\consumer\Infrastructure\Message\Processor;

use zejbm\consumer\Domain\Message\Processor\RandomBooleanGenerator;

class LinearProbabilityRandomBooleanGenerator implements RandomBooleanGenerator
{
    /**
     * @var int
     */
    private $sampleLength;

    /**
     * @var float
     */
    private $ratio;

    public function __construct(int $sampleLength, float $ratio) {
        $this->sampleLength = $sampleLength;
        $this->ratio = $ratio;
    }

    public function generate(): bool {
        $test = mt_rand(1, $this->sampleLength);
        return $test <= $this->ratio * $this->sampleLength;
    }
}