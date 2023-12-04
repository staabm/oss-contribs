<?php

namespace staabm\OssContribs\PullRequest;

readonly class PullRequestUrlParser {
    public function __construct(
        private string $url
    ) {
    }

    public function getRepoIdentifier(): string {
        $path = parse_url($this->url, PHP_URL_PATH);
        if (!is_string($path)) {
            throw new \RuntimeException('Could not parse url: ' . $this->url);
        }
        $parts = explode('/', $path);

        return $parts[1] . '/' . $parts[2];
    }

}