<?php

namespace staabm\OssContribs\PullRequest;

readonly class PullRequestUrlParser {
    public function __construct(
        private string $url
    ) {
    }

    public function getRepoIdentifier(): string {
        $path = parse_url($this->url, PHP_URL_PATH);
        $parts = explode('/', $path);

        return $parts[1] . '/' . $parts[2];
    }

}