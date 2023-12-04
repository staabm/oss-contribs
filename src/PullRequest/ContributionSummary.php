<?php

namespace staabm\OssContribs\PullRequest;

readonly class ContributionSummary {
    /**
     * @param array<RepositoryContribSummary> $repositoryReactionSummaries
     */
    public function __construct(
        public iterable $repositoryReactionSummaries,
    ) {
    }
}