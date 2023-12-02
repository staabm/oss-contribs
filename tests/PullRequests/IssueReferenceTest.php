<?php

namespace staabm\OssContribs\Tests\PullRequest;

use PHPUnit\Framework\TestCase;
use staabm\OssContribs\PullRequest\DescriptionKeyword;
use staabm\OssContribs\PullRequest\IssueReference;
use staabm\OssContribs\PullRequest\KeywordsParser;

class IssueReferenceTest extends TestCase
{
    public function testReference() {
        $issueRef = new IssueReference(DescriptionKeyword::CLOSES, 'phpstan/phpstan#10169');

        $this->assertSame('phpstan', $issueRef->getRepoOwner());
        $this->assertSame('phpstan', $issueRef->getRepoName());
        $this->assertSame(10169, $issueRef->getNumber());
    }

    public function testReference2() {
        $issueRef = new IssueReference(DescriptionKeyword::CLOSES, 'staabm/test#10169');

        $this->assertSame('staabm', $issueRef->getRepoOwner());
        $this->assertSame('test', $issueRef->getRepoName());
        $this->assertSame(10169, $issueRef->getNumber());
    }

}