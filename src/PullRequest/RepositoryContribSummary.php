<?php

namespace staabm\OssContribs\PullRequest;

readonly class RepositoryContribSummary {
    /**
     * @param iterable<PullRequest> $pullRequests
     * @param iterable<IssueReaction> $issueReactions
     */
    public function __construct(
        public string $repoName,
        public iterable $pullRequests,
        public iterable $issueReactions,
    ) {
    }
}
