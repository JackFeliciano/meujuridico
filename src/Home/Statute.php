<?php

declare(strict_types=1);

namespace MeuJuridico\Home;

/**
 * One article of legislation. The area is what the router matches on, so a
 * question about a deposit finds the tenancy articles first.
 */
final class Statute
{
    public function __construct(
        public readonly string $id,
        public readonly string $area,
        public readonly string $law,
        public readonly string $article,
        public readonly string $text,
    ) {
    }
}
