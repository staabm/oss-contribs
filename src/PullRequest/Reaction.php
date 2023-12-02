<?php

namespace staabm\OssContribs\PullRequest;

readonly class Reaction {
    public function __construct(
        public string $content // e.g. "THUMBS_UP"
    ) {
    }
}