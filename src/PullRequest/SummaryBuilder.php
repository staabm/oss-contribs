<?php

namespace staabm\OssContribs\PullRequest;

use Github\Client;
use Iterator;
use function PHPStan\dumpType;

final class SummaryBuilder {
    private const ISSUE_NOT_FOUND = 'Could not resolve to an Issue';

    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @param Iterator<PullRequest> $pullRequests
     */
    public function build(Iterator $pullRequests): ContributionSummary {
        $issueReactions = [];
        $perRepoPrs = [];

        foreach($this->chunkIterator($pullRequests, 25) as $pullsChunk) {
            /**
             * @var list<PullRequest> $pullsChunk
             */
            foreach($pullsChunk as $pr) {
                $perRepoPrs[$pr->getRepoIdentifier()] ??= [];
                $perRepoPrs[$pr->getRepoIdentifier()][] = $pr;
            }

            $result = $this->buildResult($pullsChunk);
            if (!isset($result['data'])) {
                continue;
            }

            foreach($result['data'] as $repo) {
                $repoName = $repo['nameWithOwner'];
                unset($repo['nameWithOwner']);

                if (!isset($issueReactions[$repoName])) {
                    $issueReactions[$repoName] = [];
                }

                foreach($repo as $issue) {
                    $number = $issue['number'];
                    $title = $issue['title'];
                    $reactionsCount = $issue['reactions']['totalCount'];

                    $reactions = [];
                    foreach($issue['reactions']['nodes'] as $reaction) {
                        $reactions[] = new Reaction($reaction['content']);
                    }
                    $issueReactions[$repoName][] = new IssueReaction(
                        $number,
                        $title,
                        $reactionsCount,
                        $reactions
                    );
                }
            }
        }

        return $this->buildSummary($issueReactions, $perRepoPrs);
    }

    /**
     * @param array<string, list<IssueReaction>> $issueReactions
     * @param array<string, list<PullRequest>> $perRepoPrs
     */
    public function buildSummary(array $issueReactions, array $perRepoPrs): ContributionSummary
    {
        $repositoryReactionSummaries = [];

        $issueRepos = array_keys($issueReactions);
        $prRepos = array_keys($perRepoPrs);
        foreach (array_diff($prRepos, $issueRepos) as $repoName) {
            $repositoryReactionSummaries[] = new RepositoryContribSummary($repoName, $perRepoPrs[$repoName] ?? [], []);
        }

        foreach ($issueReactions as $repoName => $issueReactions) {
            $repositoryReactionSummaries[] = new RepositoryContribSummary($repoName, $perRepoPrs[$repoName] ?? [], $issueReactions);
        }

        return new ContributionSummary($repositoryReactionSummaries);
    }

    /**
     * @param list<PullRequest> $pullRequests
     */
    private function buildQuery(array $pullRequests): ?string {
        $issuesPerRepo = [];

        foreach($pullRequests as $pr) {
            /**
             * @var IssueReference $issue
             */
            foreach ($pr->referencedIssues as $issue) {
                // replace chars not allowed in a graphql "name", see https://github.com/graphql/graphql-spec/issues/779
                $repoIdentifier = str_replace(['/', '-'], '__', $issue->getRepoIdentifier());

                if (!isset($issuesPerRepo[$repoIdentifier])) {
                    $issuesPerRepo[$repoIdentifier] = [];
                }
                $issuesPerRepo[$repoIdentifier][] = $issue;
            }
        }

        $query = '';
        foreach ($issuesPerRepo as $repoIdentifier => $issues) {
            $subQuery = '';

            $firstIssue = null;
            foreach ($issues as $issue) {
                if ($firstIssue === null) {
                    $firstIssue = $issue;
                }

                $subQuery .= 'issue' . $issue->getNumber() . ': issue(number: ' . $issue->getNumber() . ') 
                      {
                          number
                          title
                          
                          reactions(first: 10) {
                            totalCount
                            nodes {
                              id
                              content
                            }
                          }
                    }
                    ';
            }

            if ($firstIssue === null) {
                continue;
            }

            $query .= $repoIdentifier . ': repository(name: "' . $firstIssue->getRepoName() . '", owner: "' . $firstIssue->getRepoOwner() . '") {
                    nameWithOwner
                    ' . $subQuery . '
                }
                ';
        }

        if ($query === '') {
            return null;
        }

        return '{
                ' . $query . '
            }';
    }

    /**
     * @template T
     * @param Iterator<T> $it
     * @return Iterator<list<T>>
     */
    private function chunkIterator(Iterator $it, int $n): Iterator
    {
        $chunk = [];

        for($i = 0; $it->valid(); $i++){
            $chunk[] = $it->current();
            $it->next();
            if(count($chunk) == $n){
                yield $chunk;
                $chunk = [];
            }
        }

        if(count($chunk)){
            yield $chunk;
        }
    }

    /**
     * @param list<PullRequest> $pullsChunk
     */
    private function buildResult(array $pullsChunk): array
    {
        $graphql = $this->client->graphql();

        $query = $this->buildQuery($pullsChunk);
        if ($query === null) {
            return [];
        }

        $result = [];
        try {
            $result = $graphql->execute($query);
        } catch (\RuntimeException $e) {
            if (!str_contains($e->getMessage(), self::ISSUE_NOT_FOUND)) {
                throw $e;
            }

            // on issue not found, try each PR individually
            foreach ($pullsChunk as $pr) {
                $query = $this->buildQuery([$pr]);
                if ($query === null) {
                    continue;
                }
                try {
                    $result = $graphql->execute($query);
                } catch (\RuntimeException $e) {
                    if (!str_contains($e->getMessage(), self::ISSUE_NOT_FOUND)) {
                        throw $e;
                    }

                    fwrite(STDERR, "WARN: SKIP PR " . $pr->getRepoIdentifier() . "#" . $pr->number . " - " . $e->getMessage() . "\n");
                }
            }
        }

        return $result;
    }

}