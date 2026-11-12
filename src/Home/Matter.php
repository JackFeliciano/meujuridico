<?php

declare(strict_types=1);

namespace MeuJuridico\Home;

/**
 * A case file. It carries the area, the citizen it belongs to, the lawyer
 * assigned and the stage it has reached. The stage list runs in order and a
 * matter closes at the last one.
 */
final class Matter
{
    public const STAGES = ['intake', 'assigned', 'filed', 'hearing', 'decided', 'closed'];

    public function __construct(
        public readonly string $id,
        public readonly string $area,
        public readonly string $citizen,
        public readonly string $summary,
        public string $lawyer = '',
        public string $stage = 'intake',
    ) {
    }

    /**
     * The lawyer takes the matter, and the stage moves off intake here so a second
     * call changes nothing.
     */
    public function assignTo(string $handle): void
    {
        $this->lawyer = $handle;
        if ($this->stage === 'intake') {
            $this->stage = 'assigned';
        }
    }

    public function next(): void
    {
        $index = array_search($this->stage, self::STAGES, true);
        if ($index !== false && $index < count(self::STAGES) - 1) {
            $this->stage = self::STAGES[$index + 1];
        }
    }

    public function closed(): bool
    {
        return $this->stage === 'closed';
    }
}
