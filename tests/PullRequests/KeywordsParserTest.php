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
        $issues = $parser->findReferencedIssues(
            'https://github.com/staabm/oss-contribs/pull/1',
            $bodyText
        );

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

        yield [
            "for now we remove support for mysqli_fetch_object in phpstan-dba to make phpstan-dba work for all other usescases again.
in a followup I will try to get rid of the stubs in phpstan-dba so we don't depend on changes of stubs in phpstans-src
closes #631",
            [
                new IssueReference(DescriptionKeyword::CLOSES, 'staabm/oss-contribs#631'),
            ]
        ];
        yield [
            "closes #597",
            [
                new IssueReference(DescriptionKeyword::CLOSES, 'staabm/oss-contribs#597')
            ]
        ];
        yield [
            "closes #5755 (comment)",
            [
                new IssueReference(DescriptionKeyword::CLOSES, 'staabm/oss-contribs#5755')
            ]
        ];


        yield ["some drive-by cleanup to ease reading", []];
        yield ["followup to #2779", []];
    }
}