<?php

declare(strict_types=1);

namespace Tests\Release;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ChangelogWorkflowTest extends TestCase
{
    private const REPOSITORY_URL = 'https://github.com/mollie/mollie-api-php';

    #[DataProvider('releaseEventProvider')]
    #[Test]
    public function workflow_uses_canonical_release_event_identity(string $fixture, bool $prerelease): void
    {
        $event = $this->releaseEvent($fixture);
        $workflow = $this->workflow();

        $this->assertSame('main', $event['repository']['default_branch']);
        $this->assertSame($prerelease, $event['release']['prerelease']);
        $this->assertNotSame($event['release']['tag_name'], $event['release']['name']);
        $this->assertStringContainsString('ref: ${{ github.event.repository.default_branch }}', $workflow);
        $this->assertStringContainsString('branch: ${{ github.event.repository.default_branch }}', $workflow);
        $this->assertStringContainsString('latest-version: ${{ github.event.release.tag_name }}', $workflow);
        $this->assertDoesNotMatchRegularExpression('/(?:ref|branch):\s*(?:master|main)\b/', $workflow);
        $this->assertStringNotContainsString('github.event.release.name', $workflow);
    }

    public static function releaseEventProvider(): array
    {
        return [
            'prerelease with a human title' => ['published-prerelease.json', true],
            'stable release with a human title' => ['published-stable.json', false],
        ];
    }

    #[Test]
    public function workflow_subscribes_to_published_so_draft_published_prereleases_are_not_missed(): void
    {
        $workflow = $this->workflow();

        // GitHub does not emit `prereleased` for a pre-release published from a
        // draft, and never emits `released` for a pre-release at all. `published`
        // is the only type that covers every publication route.
        $this->assertMatchesRegularExpression('/types:\s*\[published\]/', $workflow);
        $this->assertDoesNotMatchRegularExpression('/types:[^\]]*\bprereleased\b/', $workflow);
        $this->assertDoesNotMatchRegularExpression('/types:[^\]]*\breleased\b/', $workflow);
        $this->assertMatchesRegularExpression('/permissions:\s*\R\s+contents:\s*write/', $workflow);
    }

    #[Test]
    public function changelog_has_one_leading_unreleased_section_and_chronological_releases(): void
    {
        $changelog = $this->readFile($this->repositoryPath('CHANGELOG.md'));
        preg_match_all('/^## .+$/m', $changelog, $allHeadingMatches);
        preg_match_all('/^## \[[^\]]+\].*$/m', $changelog, $releaseHeadingMatches);
        $headings = $releaseHeadingMatches[0];

        $this->assertNotEmpty($headings);
        $this->assertCount(1, array_filter($headings, static fn (string $heading) => str_starts_with($heading, '## [Unreleased](')));
        $this->assertStringStartsWith('## [Unreleased](', $allHeadingMatches[0][0]);

        $dates = [];
        foreach (array_slice($headings, 1) as $heading) {
            $this->assertMatchesRegularExpression(
                '/^## \[v\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?\]\([^\n]+\) - (\d{4}-\d{2}-\d{2})$/',
                $heading
            );
            preg_match('/(\d{4}-\d{2}-\d{2})$/', $heading, $dateMatch);
            $dates[] = $dateMatch[1];
        }

        $sortedDates = $dates;
        rsort($sortedDates);
        $this->assertSame($sortedDates, $dates);
    }

    #[Test]
    public function updater_transformation_is_idempotent(): void
    {
        $event = $this->releaseEvent('published-prerelease.json');
        $before = $this->fixture('changelog-before.md');
        $expected = $this->fixture('changelog-after.md');

        $updated = $this->applyChangelogUpdater($before, $event);

        $this->assertSame($expected, $updated);
        $this->assertSame($updated, $this->applyChangelogUpdater($updated, $event));
    }

    private function applyChangelogUpdater(string $changelog, array $event): string
    {
        $tag = $event['release']['tag_name'];
        if (preg_match('/^## \['.preg_quote($tag, '/').'\]/m', $changelog) === 1) {
            return $changelog;
        }

        $pattern = '/^## \[Unreleased\]\('.preg_quote(self::REPOSITORY_URL, '/').'\/compare\/([^\n]+)\.\.\.HEAD\)\R(.*?)(?=^## \[)/ms';
        preg_match($pattern, $changelog, $match);
        $this->assertNotEmpty($match, 'The fixture must contain a linked Unreleased section.');

        $date = substr($event['release']['published_at'], 0, 10);
        $replacement = sprintf(
            "## [Unreleased](%s/compare/%s...HEAD)\n\n## [%s](%s/compare/%s...%s) - %s\n%s",
            self::REPOSITORY_URL,
            $tag,
            $tag,
            self::REPOSITORY_URL,
            $match[1],
            $tag,
            $date,
            $match[2]
        );

        return preg_replace($pattern, $replacement, $changelog, 1) ?? $changelog;
    }

    private function releaseEvent(string $fixture): array
    {
        return json_decode($this->fixture($fixture), true, flags: JSON_THROW_ON_ERROR);
    }

    private function fixture(string $name): string
    {
        return $this->readFile($this->repositoryPath('tests/Fixtures/Release/'.$name));
    }

    private function workflow(): string
    {
        return $this->readFile($this->repositoryPath('.github/workflows/update-changelog.yml'));
    }

    private function repositoryPath(string $path): string
    {
        return dirname(__DIR__, 2).'/'.$path;
    }

    private function readFile(string $path): string
    {
        $contents = file_get_contents($path);
        $this->assertNotFalse($contents);

        return $contents;
    }
}
