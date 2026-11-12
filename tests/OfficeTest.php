<?php

declare(strict_types=1);

namespace MeuJuridico\Tests;

use MeuJuridico\Home\Matter;
use MeuJuridico\Home\Office;
use MeuJuridico\Home\Question;
use MeuJuridico\Home\Sample;
use MeuJuridico\Home\Statute;

return [
    ['an article needs text', static function (): void {
        $office = new Office();
        try {
            $office->addStatute(new Statute('S-1', 'tenancy', 'Law', '1', '   '));
            Assert::true(false, 'an empty article was accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['a lawyer needs an area', static function (): void {
        $office = new Office();
        try {
            $office->addLawyer('dra.nobody', []);
            Assert::true(false, 'an area-less lawyer was accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['a matter cannot open twice', static function (): void {
        $office = new Office();
        $office->open(new Matter('M-1', 'tenancy', 'Ana', 'Deposit'));
        try {
            $office->open(new Matter('M-1', 'tenancy', 'Ana', 'Deposit again'));
            Assert::true(false, 'a duplicate matter was accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['search finds the article on point', static function (): void {
        $office = Sample::office();
        $hits = $office->search('the landlord raised the rent during the lease', 'tenancy');
        Assert::true($hits !== []);
        Assert::same('ST-01', $hits[0]->id);
    }],
    ['search stays inside the area asked', static function (): void {
        $office = Sample::office();
        foreach ($office->search('deposit', 'labour') as $statute) {
            Assert::same('labour', $statute->area);
        }
    }],
    ['short words are ignored', static function (): void {
        $office = Sample::office();
        Assert::count(0, $office->search('do it to me'));
    }],
    ['an answer names the law and the article', static function (): void {
        $office = Sample::office();
        $answer = $office->answer(new Question('How much surcharge does overtime carry?', 'labour', 'Joao'));
        Assert::contains('Consolidacao das Leis do Trabalho', $answer);
        Assert::contains('article 59', $answer);
    }],
    ['an answer with nothing on point says so', static function (): void {
        $office = Sample::office();
        $answer = $office->answer(new Question('parking permit for a tractor', 'consumer', 'Ana'));
        Assert::contains('holds no article', $answer);
    }],
    ['routing picks the lawyer carrying the fewest cases in that area', static function (): void {
        $office = new Office();
        $office->addLawyer('dra.helena', ['tenancy']);
        $office->addLawyer('dra.bianca', ['tenancy']);
        $office->open(new Matter('M-1', 'tenancy', 'Ana', 'One'));
        $office->open(new Matter('M-2', 'tenancy', 'Bruno', 'Two'));
        $office->open(new Matter('M-3', 'tenancy', 'Caio', 'Three'));
        $office->assign('M-1');
        $office->assign('M-2');
        $office->matter('M-1')->assignTo('dra.helena');
        $office->matter('M-2')->assignTo('dra.helena');
        $office->open(new Matter('M-4', 'tenancy', 'Dora', 'Four'));
        Assert::same('dra.bianca', $office->assign('M-4'));
        Assert::same(2, $office->areaCount('dra.helena', 'tenancy'));
    }],
    ['an area with no lawyer waits', static function (): void {
        $office = new Office();
        $office->addLawyer('dr.marcos', ['labour']);
        $office->open(new Matter('M-1', 'tenancy', 'Ana', 'Deposit'));
        Assert::same(null, $office->assign('M-1'));
        Assert::same('intake', $office->matter('M-1')->stage);
    }],
    ['assigning moves the matter off intake once', static function (): void {
        $office = new Office();
        $office->addLawyer('dra.helena', ['tenancy']);
        $office->open(new Matter('M-1', 'tenancy', 'Ana', 'Deposit'));
        $office->assign('M-1');
        Assert::same('assigned', $office->matter('M-1')->stage);
        $office->assign('M-1');
        Assert::same('assigned', $office->matter('M-1')->stage);
    }],
    ['a closed matter carries no load', static function (): void {
        $office = new Office();
        $office->addLawyer('dra.helena', ['tenancy']);
        $office->open(new Matter('M-1', 'tenancy', 'Ana', 'Deposit'));
        $office->assign('M-1');
        Assert::same(1, $office->load('dra.helena'));
        for ($i = 0; $i < 8; $i++) {
            $office->matter('M-1')->next();
        }
        Assert::true($office->matter('M-1')->closed());
        Assert::same(0, $office->load('dra.helena'));
    }],
    ['the sample office leaves one matter closed', static function (): void {
        $office = Sample::office();
        Assert::count(5, $office->matters());
        Assert::count(4, $office->openMatters());
    }],
];
