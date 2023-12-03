<?php

namespace staabm\OssContribs\Tests\PullRequest;

use PHPUnit\Framework\TestCase;
use staabm\OssContribs\PullRequest\DescriptionKeyword;
use staabm\OssContribs\PullRequest\IssueReference;
use staabm\OssContribs\PullRequest\KeywordsParser;
use staabm\OssContribs\PullRequest\PullRequest;

class PullRequestTest extends TestCase
{
    public function test() {
        $pr = new PullRequest(
            2393,
            'Update dependency phpunit/phpunit to v9.6.15 (1.10.x)',
            'closes phpstan/phpstan#10169',
            'https://github.com/phpstan/phpstan-src/pull/2393',
            []

        );
        $this->assertEquals('phpstan/phpstan-src', $pr->getRepoIdentifier());
    }

}