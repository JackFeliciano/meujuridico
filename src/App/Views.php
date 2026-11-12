<?php

declare(strict_types=1);

namespace MeuJuridico\App;

use MeuJuridico\Home\Office;
use MeuJuridico\Home\Sample;

final class Views
{
    public function __construct(private readonly string $base)
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$this->h($title)}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function notFound(string $path): string
    {
        return $this->header('Not found')
            . '<h1>Not found</h1>' . "\n"
            . '<p class="stage">' . $this->h($path) . ' is not a route here.</p>' . "\n"
            . $this->footer();
    }

    public function front(Sample $sample, string $today): string
    {
        $office = $sample->office();
        $out = $this->header('MeuJuridico');
        $out .= '<h1>MeuJuridico</h1>' . "\n";
        $out .= '<p class="lede">A legal office online. The citizen asks, the assistant finds the article, and a lawyer takes the case in the area it belongs to.</p>' . "\n";
        $out .= '<p class="stage">Office as of ' . $this->h($today) . '.</p>' . "\n";

        $out .= '<h2>Questions and what the office answers</h2>' . "\n";
        $out .= '<table class="tiers">' . "\n";
        $out .= '<thead><tr><th>Question</th><th>Area</th><th>Answer</th></tr></thead>' . "\n";
        $out .= '<tbody>' . "\n";
        foreach ($sample->questions() as $question) {
            $out .= '<tr><td>' . $this->h($question->text) . '</td>';
            $out .= '<td>' . $this->h($question->area) . '</td>';
            $out .= '<td>' . $this->h($office->answer($question)) . '</td></tr>' . "\n";
        }
        $out .= '</tbody>' . "\n" . '</table>' . "\n";

        $out .= '<h2>Legislation in the index</h2>' . "\n";
        $out .= '<div class="cards">' . "\n";
        foreach ($office->search('deposit lease rent', 'tenancy') as $statute) {
            $out .= '<article class="card">' . "\n";
            $out .= '<h3>' . $this->h($statute->law) . ', article ' . $this->h($statute->article) . '</h3>' . "\n";
            $out .= '<p>' . $this->h($statute->text) . '</p>' . "\n";
            $out .= '</article>' . "\n";
        }
        $out .= '</div>' . "\n";

        $out .= $this->matters($office);
        $out .= $this->lawyers($office);
        $out .= $this->footer();

        return $out;
    }

    private function matters(Office $office): string
    {
        $out = '<h2>Case files</h2>' . "\n";
        $out .= '<table class="tiers">' . "\n";
        $out .= '<thead><tr><th>Matter</th><th>Area</th><th>Citizen</th><th>Lawyer</th><th>Stage</th></tr></thead>' . "\n";
        $out .= '<tbody>' . "\n";
        foreach ($office->matters() as $matter) {
            $out .= '<tr><td>' . $this->h($matter->summary) . '</td>';
            $out .= '<td>' . $this->h($matter->area) . '</td>';
            $out .= '<td>' . $this->h($matter->citizen) . '</td>';
            $out .= '<td>' . ($matter->lawyer !== '' ? $this->h($matter->lawyer) : 'unassigned') . '</td>';
            $out .= '<td><span class="badge ' . ($matter->closed() ? 'off' : 'on') . '">'
                . $this->h($matter->stage) . '</span></td></tr>' . "\n";
        }
        $out .= '</tbody>' . "\n" . '</table>' . "\n";

        return $out;
    }

    private function lawyers(Office $office): string
    {
        $out = '<h2>Who carries what</h2>' . "\n";
        $out .= '<dl>';
        foreach (['dra.helena', 'dr.marcos', 'dra.bianca'] as $handle) {
            $out .= '<dt>' . $this->h($handle) . '</dt><dd>' . $office->load($handle) . ' live cases</dd>';
        }
        $out .= '</dl>' . "\n";

        return $out;
    }
}
