<?php

namespace staabm\OssContribs\PullRequest;

readonly class PullRequest {
    /**
     * @param iterable<IssueReference> $referencedIssues
     */
    public function __construct(
        public int $number,
        public string $title,
        public string $body,
        public string $url,
        public iterable $referencedIssues,
    ) {
    }

    public function getRepoIdentifier(): string {
        return (new PullRequestUrlParser($this->url))->getRepoIdentifier();
    }

}