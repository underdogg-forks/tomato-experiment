<?php

namespace App\Clients;

use App\DataTransferObjects\Issue;
use App\DataTransferObjects\IssueOwner;
use App\DataTransferObjects\Label;
use App\DataTransferObjects\Reaction;
use App\DataTransferObjects\Repository;
use App\Exceptions\GitHubRateLimitException;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;

class GitHubIssues extends GitHub
{
    /**
     * @return array<Issue>
     *
     * @throws GitHubRateLimitException
     */
    public function getIssuesFromGitHubApi(Repository $repo): array
    {
        $fullRepoName = $repo->owner . '/' . $repo->name;

        $result = $this->request('GET', 'repos/' . $fullRepoName . '/issues');

        if ( ! $result->successful()) {
            return $this->handleUnsuccessfulIssueRequest($result, $fullRepoName);
        }

        $fetchedIssues = $result->json();

        return collect($fetchedIssues)
            ->map(fn (array $fetchedIssue): Issue => $this->parseIssue($repo, $fetchedIssue))
            ->all();
    }

    private function parseIssue(Repository $repo, array $fetchedIssue): Issue
    {
        $repoName = $repo->owner . '/' . $repo->name;

        return new Issue(
            id: $fetchedIssue['id'],
            number: $fetchedIssue['number'],
            repoName: $repoName,
            repoUrl: 'https://github.com/' . $repoName,
            title: $fetchedIssue['title'],
            url: $fetchedIssue['html_url'],
            body: $fetchedIssue['body'],
            labels: $this->getIssueLabels($fetchedIssue),
            reactions: $this->getIssueReactions($fetchedIssue),
            commentCount: $fetchedIssue['comments'],
            createdAt: Carbon::parse($fetchedIssue['created_at']),
            createdBy: $this->getIssueOwner($fetchedIssue),
            isPullRequest: ! empty($fetchedIssue['pull_request']),
        );
    }

    private function getIssueOwner(array $fetchedIssue): IssueOwner
    {
        // Set avatar size to 48px
        $fetchedIssue['user']['avatar_url'] .= (parse_url($fetchedIssue['user']['avatar_url'], PHP_URL_QUERY) ? '&' : '?') . 's=48';

        return new IssueOwner(
            name: $fetchedIssue['user']['login'],
            url: $fetchedIssue['user']['html_url'],
            profilePictureUrl: $fetchedIssue['user']['avatar_url'],
        );
    }

    private function getIssueLabels(array $fetchedIssue): array
    {
        return collect($fetchedIssue['labels'])
            ->map(function (array $label): Label {
                return new Label(
                    name: $label['name'],
                    color: '#' . $label['color'],
                );
            })->toArray();
    }

    private function getIssueReactions(array $fetchedIssue): array
    {
        $emojis = config('repos.reactions');

        return collect($fetchedIssue['reactions'])
            ->only(array_keys($emojis))
            ->map(function (int $count, string $content) use ($emojis): Reaction {
                return new Reaction(
                    content: $content,
                    count: $count,
                    emoji: $emojis[$content]
                );
            })
            ->values()
            ->all();
    }

    /**
     * @throws GitHubRateLimitException
     */
    private function handleUnsuccessfulIssueRequest(Response $response, string $fullRepoName): array
    {
        return match ($response->status()) {
            404     => $this->handleNotFoundResponse($fullRepoName),
            403     => $this->handleForbiddenResponse($response, $fullRepoName),
            default => [],
        };
    }

    private function handleNotFoundResponse(string $fullRepoName): array
    {
        report($fullRepoName . ' is not a valid GitHub repo.');

        return [];
    }

    /**
     * @throws GitHubRateLimitException
     */
    private function handleForbiddenResponse(Response $response, string $fullRepoName): array
    {
        if ($response->header('X-RateLimit-Remaining') === '0') {
            throw new GitHubRateLimitException('GitHub API rate limit reached!');
        }

        report($fullRepoName . ' is a forbidden GitHub repo.');

        return [];
    }
}
