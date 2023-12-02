<?php

namespace staabm\OssContribs\PullRequest;

readonly class PullRequest {
    /**
     * @param iterable<IssueReference> $referencedIssues
     */
    public function __construct(
        public int $number,
        public string $body,
        public string $url,
        public iterable $referencedIssues,
    ) {
    }
}