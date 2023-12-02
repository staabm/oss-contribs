<?php

namespace staabm\OssContribs\PullRequest;

readonly class ContributionSummary {
    /**
     * @param iterable<RepositoryReactionSummary> $repositoryReactionSummaries
     */
    public function __construct(
        public iterable $repositoryReactionSummaries,
    ) {
    }
}