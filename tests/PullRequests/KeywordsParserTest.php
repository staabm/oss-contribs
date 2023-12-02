<?php

namespace staabm\OssContribs\Tests\PullRequest;

use PHPUnit\Framework\TestCase;
use staabm\OssContribs\PullRequest\DescriptionKeyword;
use staabm\OssContribs\PullRequest\IssueReference;
use staabm\OssContribs\PullRequest\KeywordsParser;

class KeywordsParserTest extends TestCase
{
    /**
     * @dataProvider provideFindReferencedIssues
     */
    public function testParser(string $bodyText, $expected) {
        $parser = new KeywordsParser();
        $issues = $parser->findReferencedIssues($bodyText);

        $this->assertSame(count($expected), count($issues));
        $this->assertEquals($expected, $issues);
    }

    static public function provideFindReferencedIssues() {
        yield [
            "closes phpstan/phpstan#10169",
            [
                new IssueReference(DescriptionKeyword::CLOSES, 'phpstan/phpstan#10169')
            ]
        ];
        yield [
            "fixes phpstan/phpstan#9778\nfixes phpstan/phpstan#9723\nfixes phpstan/phpstan#6407",
            [
                new IssueReference(DescriptionKeyword::FIXES, 'phpstan/phpstan#9778'),
                new IssueReference(DescriptionKeyword::FIXES, 'phpstan/phpstan#9723'),
                new IssueReference(DescriptionKeyword::FIXES, 'phpstan/phpstan#6407'),
            ]
        ];
        yield ["some drive-by cleanup to ease reading", []];
        yield ["followup to #2779", []];
    }
}