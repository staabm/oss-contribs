<?php

namespace staabm\OssContribs\PullRequest;

/**
 * @see https://docs.github.com/en/get-started/writing-on-github/working-with-advanced-formatting/using-keywords-in-issues-and-pull-requests
 */
enum DescriptionKeyword: string
{
    case CLOSE = 'close';
    case CLOSES = 'closes';
    case CLOSED = 'closed';
    case FIX = 'fix';
    case FIXES = 'fixes';
    case FIXED = 'fixed';
    case RESOLVE = 'resolve';
    case RESOLVES = 'resolves';
    case RESOLVED = 'resolved';
}
