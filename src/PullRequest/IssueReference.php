<?php

namespace staabm\OssContribs\PullRequest;

readonly class IssueReference
{
    public function __construct(
        public DescriptionKeyword $keyword,
        public string $issueRef
    )
    {
    }

    public function getUrl(): string
    {
        return 'https://github.com/' . $this->issueRef;
    }

    public function getRepoOwner(): string {
        $prefix = explode('#', $this->issueRef)[0];

        return explode('/', $prefix)[0];
    }

    public function getRepoName(): string {
        $prefix = explode('#', $this->issueRef)[0];

        return explode('/', $prefix)[1];
    }

    public function getRepoIdentifier(): string {
        $prefix = explode('#', $this->issueRef)[0];

        return $prefix;
    }

    public function getNumber(): int {
        return (int) explode('#', $this->issueRef)[1];
    }
}