<?php

namespace staabm\OssContribs\PullRequest;

readonly class RepositoryContribSummary {
    /**
     * @param array<IssueReaction> $issueReactions
     */
    public function __construct(
        public string $repoName,
        public int $prCount,
        public iterable $issueReactions,
    ) {
    }
}
