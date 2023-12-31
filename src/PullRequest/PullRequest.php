<?php

namespace staabm\OssContribs\PullRequest;

readonly class PullRequest {
    /**
     * @param list<IssueReference> $referencedIssues
     */
    public function __construct(
        public int $number,
        public string $title,
        public string $body,
        public string $url,
        public array $referencedIssues,
    ) {
    }

    public function getRepoIdentifier(): string {
        return (new PullRequestUrlParser($this->url))->getRepoIdentifier();
    }

}