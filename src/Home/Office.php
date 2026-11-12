<?php

declare(strict_types=1);

namespace MeuJuridico\Home;

use InvalidArgumentException;

/**
 * The office. It holds the legislation, the case files and the lawyers, answers
 * a question from the articles it can find, and routes a matter to a lawyer who
 * works that area.
 */
final class Office
{
    /**
     * Words a question carries that name no subject. They stay out of the match,
     * so a question about a parking permit does not reach a consumer article
     * through the word for.
     */
    private const STOP = ['the', 'and', 'for', 'with', 'that', 'this', 'when', 'how', 'does', 'did',
        'can', 'may', 'must', 'shall', 'from', 'into', 'after', 'before', 'within', 'much', 'many',
        'what', 'which', 'who', 'why', 'are', 'was', 'were', 'has', 'have', 'had', 'not', 'but',
        'its', 'their', 'your', 'you', 'him', 'her', 'them', 'they', 'she', 'his', 'our', 'out'];

    /** @var array<string, Statute> */
    private array $statutes = [];

    /** @var array<string, Matter> */
    private array $matters = [];

    /** @var array<string, list<string>> lawyer handle to areas */
    private array $lawyers = [];

    public function addStatute(Statute $statute): void
    {
        if (trim($statute->text) === '') {
            throw new InvalidArgumentException('An article needs text.');
        }
        $this->statutes[$statute->id] = $statute;
    }

    public function addLawyer(string $handle, array $areas): void
    {
        if ($areas === []) {
            throw new InvalidArgumentException('A lawyer needs at least one area.');
        }
        $this->lawyers[$handle] = array_values($areas);
    }

    public function open(Matter $matter): void
    {
        if (isset($this->matters[$matter->id])) {
            throw new InvalidArgumentException('A matter with id ' . $matter->id . ' is already open.');
        }
        $this->matters[$matter->id] = $matter;
    }

    public function matter(string $id): Matter
    {
        if (!isset($this->matters[$id])) {
            throw new InvalidArgumentException('No matter with id ' . $id . '.');
        }

        return $this->matters[$id];
    }

    /**
     * The articles a question reaches. A word of three letters or more counts
     * when the article text carries it, so a question about a deposit finds the
     * tenancy articles and nothing else.
     *
     * @return list<Statute>
     */
    public function search(string $question, string $area = ''): array
    {
        $words = [];
        foreach (preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($question)) ?: [] as $word) {
            if (mb_strlen($word) >= 3 && !in_array($word, self::STOP, true)) {
                $words[$word] = true;
            }
        }
        if ($words === []) {
            return [];
        }

        $hits = [];
        foreach ($this->statutes as $statute) {
            if ($area !== '' && $statute->area !== $area) {
                continue;
            }
            $body = mb_strtolower($statute->law . ' ' . $statute->article . ' ' . $statute->text);
            $score = 0;
            foreach (array_keys($words) as $word) {
                if (str_contains($body, $word)) {
                    $score++;
                }
            }
            if ($score > 0) {
                $hits[] = [$score, $statute];
            }
        }

        usort($hits, static function (array $a, array $b): int {
            return [$b[0], $a[1]->id] <=> [$a[0], $b[1]->id];
        });

        return array_map(static fn (array $hit): Statute => $hit[1], $hits);
    }

    /**
     * The answer the assistant gives: the strongest article, or a plain note that
     * the office holds nothing on point.
     */
    public function answer(Question $question): string
    {
        $hits = $this->search($question->text, $question->area);
        if ($hits === []) {
            return 'The office holds no article on that question. A lawyer reads it first.';
        }
        $best = $hits[0];

        return $best->law . ', article ' . $best->article . ': ' . $best->text;
    }

    /**
     * The lawyer a matter should reach: the one working that area with the fewest
     * open cases, ties broken by handle. An area with no lawyer returns null and
     * the matter waits.
     */
    public function route(Matter $matter): ?string
    {
        $candidates = [];
        foreach ($this->lawyers as $handle => $areas) {
            if (in_array($matter->area, $areas, true)) {
                $candidates[] = $handle;
            }
        }
        if ($candidates === []) {
            return null;
        }
        sort($candidates);
        $self = $this;
        $area = $matter->area;
        usort($candidates, static function (string $a, string $b) use ($self, $area): int {
            return [$self->load($a), $self->areaCount($a, $area), $a]
                <=> [$self->load($b), $self->areaCount($b, $area), $b];
        });

        return $candidates[0];
    }

    public function assign(string $matterId): ?string
    {
        $matter = $this->matter($matterId);
        if ($matter->lawyer !== '') {
            return $matter->lawyer;
        }
        $handle = $this->route($matter);
        if ($handle === null) {
            return null;
        }
        $matter->assignTo($handle);

        return $handle;
    }

    /**
     * How many live cases one lawyer carries. A closed matter does not count.
     */
    public function load(string $handle): int
    {
        $count = 0;
        foreach ($this->matters as $matter) {
            if ($matter->lawyer === $handle && !$matter->closed()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * How many live matters one lawyer carries inside one area. The router reads
     * this second, so a lawyer with fewer cases overall does not take work away
     * from the one who already knows that area.
     */
    public function areaCount(string $handle, string $area): int
    {
        $count = 0;
        foreach ($this->matters as $matter) {
            if ($matter->lawyer === $handle && $matter->area === $area && !$matter->closed()) {
                $count++;
            }
        }

        return $count;
    }

    /** @return list<Matter> */
    public function matters(): array
    {
        return array_values($this->matters);
    }

    /** @return list<Matter> */
    public function openMatters(): array
    {
        $out = [];
        foreach ($this->matters as $matter) {
            if (!$matter->closed()) {
                $out[] = $matter;
            }
        }

        return $out;
    }
}
