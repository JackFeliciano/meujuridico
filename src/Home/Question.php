<?php

declare(strict_types=1);

namespace MeuJuridico\Home;

/**
 * A citizen's question, in the words they used. The office reads the words for
 * an area and, when the case is heavy enough, routes it to a lawyer.
 */
final class Question
{
    public function __construct(
        public readonly string $text,
        public readonly string $area,
        public readonly string $citizen,
    ) {
    }
}
