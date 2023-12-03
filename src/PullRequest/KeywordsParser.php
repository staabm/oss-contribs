<?php

namespace staabm\OssContribs\PullRequest;

final class KeywordsParser
{
    /**
     * @return array<IssueReference>
     */
    static public function findReferencedIssues(string $bodyText): array
    {
        $keywords = [];
        foreach(DescriptionKeyword::cases() as $case) {
            $keywords[] = $case->value;
        }

        // find "closes phpstan/phpstan#10169"
        $matches = [];
        preg_match_all('{('. implode('|', $keywords) .')\s+([a-z]+/[a-z]+#[0-9]+)}', $bodyText, $matches);

        $issues = [];
        foreach($matches[0] as $i => $match) {
            $issues[] = new IssueReference(
                DescriptionKeyword::from($matches[1][$i]),
                $matches[2][$i]
            );
        }

        // find urls "closes https://github.com/phpstan/phpstan/issues/8366"
        $matches = [];
        preg_match_all('{('. implode('|', $keywords) .')\s+https://github.com/([a-z]+/[a-z]+)/issues/([0-9]+)}', $bodyText, $matches);

        foreach($matches[0] as $i => $match) {
            $issues[] = new IssueReference(
                DescriptionKeyword::from($matches[1][$i]),
                $matches[2][$i].'#'.$matches[3][$i]
            );
        }

        return $issues;
    }
}