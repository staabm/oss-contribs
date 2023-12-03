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
        $path = parse_url($this->url, PHP_URL_PATH);
        $parts = explode('/', $path);

        return $parts[1] . '/' . $parts[2];
    }

}