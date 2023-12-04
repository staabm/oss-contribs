<?php

namespace staabm\OssContribs\PullRequest;

readonly class RepositoryContribSummary {
    /**
     * @param array<PullRequest> $pullRequests
     * @param array<IssueReaction> $issueReactions
     */
    public function __construct(
        public string $repoName,
        public array $pullRequests,
        public array $issueReactions,
    ) {
    }
}
