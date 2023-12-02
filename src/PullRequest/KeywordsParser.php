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

        $matches = [];
        preg_match_all('{('. implode('|', $keywords) .')\s+([a-z]+/[a-z]+#[0-9]+)}', $bodyText, $matches);

        $issues = [];
        foreach($matches[0] as $i => $match) {
            $issues[] = new IssueReference(
                DescriptionKeyword::from($matches[1][$i]),
                $matches[2][$i]
            );
        }
        return $issues;
    }
}