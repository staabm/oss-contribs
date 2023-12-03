<?php

namespace staabm\OssContribs\PullRequest;

use Github\Client;
use Iterator;

final class SummaryBuilder {
    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @param iterable<PullRequest> $pullRequests
     */
    public function build(iterable $pullRequests): ContributionSummary {
        $issueReactions = [];
        $perRepoPrs = [];

        $graphql = $this->client->graphql();
        foreach($this->chunkIterator($pullRequests, 25) as $pullsChunk) {
            foreach($pullsChunk as $pr) {
                $perRepoPrs[$pr->getRepoIdentifier()] ??= [];

                $perRepoPrs[$pr->getRepoIdentifier()][] = $pr;
            }

            $query = $this->buildQuery($pullsChunk);
            if ($query === null) {
                continue;
            }

            $result = $graphql->execute($query);
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

        $repositoryReactionSummaries = [];

        $issueRepos = array_keys($issueReactions);
        $prRepos = array_keys($perRepoPrs);
        foreach(array_diff($prRepos, $issueRepos) as $repoName) {
            $repositoryReactionSummaries[] = new RepositoryContribSummary($repoName, $perRepoPrs[$repoName] ?? [], []);
        }

        foreach($issueReactions as $repoName => $issueReactions) {
            $repositoryReactionSummaries[] = new RepositoryContribSummary($repoName, $perRepoPrs[$repoName] ?? [], $issueReactions);
        }

        return new ContributionSummary($repositoryReactionSummaries);
    }

    /**
     * @param iterable<PullRequest> $pullRequests
     */
    private function buildQuery(iterable $pullRequests): ?string {
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
     * @return Iterator<T>
     */
    private function chunkIterator(Iterator $it, int $n)
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

}