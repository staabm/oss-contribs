<?php

namespace staabm\OssContribs\PullRequest;

readonly class RepositoryReactionSummary {
    /**
     * @param array<IssueReaction> $issueReactions
     */
    public function __construct(
        public string $repoName,
        public iterable $issueReactions,
    ) {
    }
}
