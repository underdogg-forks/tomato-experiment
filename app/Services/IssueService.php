<?php

namespace App\Services;

use App\DataTransferObjects\Issue;
use App\DataTransferObjects\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final readonly class IssueService
{
    /**
     * Get all the issues for displaying.
     *
     * @return Collection<Issue>
     */
    public function getAll(): Collection
    {
        return app(RepoService::class)
            ->reposToCrawl()
            ->flatMap(fn (Repository $repo): array => $this->getIssuesForRepo($repo));
    }

    /**
     * @return array<Issue>
     */
    public function getIssuesForRepo(Repository $repo, bool $forceRefresh = false): array
    {
        $cacheKey = $repo->owner . '/' . $repo->name;

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        $fetchedIssues = Cache::remember(
            $cacheKey,
            now()->addMinutes(120),
            fn (): array => app(GitHubIssues::class)->getIssuesFromGitHubApi($repo),
        );

        return collect($fetchedIssues)
            ->filter(fn (Issue $issue): bool => $this->shouldIncludeIssue($issue))
            ->all();
    }

    private function shouldIncludeIssue(Issue $fetchedIssue): bool
    {
        return ! $fetchedIssue->isPullRequest
            && $this->includesAtLeastOneLabel($fetchedIssue, (array) config('repos.labels'));
    }

    private function includesAtLeastOneLabel(Issue $fetchedIssue, array $labels): bool
    {
        $issueLabels = Arr::pluck($fetchedIssue->labels, 'name');

        return array_intersect($issueLabels, $labels) !== [];
    }
}
