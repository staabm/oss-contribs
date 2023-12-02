<?php

namespace staabm\OssContribs\PullRequest;

readonly class IssueReaction {
    /**
     * @param iterable<Reaction> $reactions
     */
    public function __construct(
        public int $number,
        public string $title,
        public int $reactionsCount,
        public iterable $reactions,
    ) {
    }
}