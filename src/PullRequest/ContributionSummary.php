<?php

namespace staabm\OssContribs\PullRequest;

readonly class ContributionSummary {
    /**
     * @param iterable<RepositoryContribSummary> $repositoryReactionSummaries
     */
    public function __construct(
        public iterable $repositoryReactionSummaries,
    ) {
    }
}