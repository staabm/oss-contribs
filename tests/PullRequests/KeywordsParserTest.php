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
            "motivation: ease impl of https://github.com/phpstan/phpstan-src/pull/2657 by moving the expr-var loop before the context true/false branches which simplifies the branch cases

closes https://github.com/phpstan/phpstan/issues/8366
closes https://github.com/phpstan/phpstan/issues/10064",
            [
                new IssueReference(DescriptionKeyword::CLOSES, 'phpstan/phpstan#8366'),
                new IssueReference(DescriptionKeyword::CLOSES, 'phpstan/phpstan#10064'),
            ]
        ];
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