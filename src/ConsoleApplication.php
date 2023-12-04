<?php

namespace staabm\OssContribs;

use Github\AuthMethod;
use Github\Client;
use staabm\OssContribs\PullRequest\PullRequestFilter;
use staabm\OssContribs\PullRequest\SummaryBuilder;
use Symfony\Component\HttpClient\HttplugClient;

final class ConsoleApplication {
    /**
     * @param int<1950, 2050> $year
     */
    public function run(Client $client, string $username, int $year):void {

        // graphql cheat sheet at https://medium.com/@tharshita13/github-graphql-api-cheatsheet-38e916fe76a3
        // examples at https://gist.github.com/MichaelCurrin/f8a7a11451ce4ec055d41000c915b595#resources
        $pullRequestFilter = new PullRequestFilter($client);
        $pullRequests = $pullRequestFilter->search("is:pr is:public is:merged sort:updated-desc author:". $username ." created:".$year);

        $reactionsFilter = new SummaryBuilder($client);
        $contribSummary = $reactionsFilter->build($pullRequests);


        $totalRepoCount = 0;
        $totalPrCount = 0;
        $totalIssueCount = 0;
        $totalReactionsCount = 0;
        foreach($contribSummary->repositoryReactionSummaries as $repoReactionSummary) {
            $sumReactions = 0;
            foreach($repoReactionSummary->issueReactions as $issueReaction) {
                $sumReactions += $issueReaction->reactionsCount;
            }

            $metrics = [];
            if ($sumReactions > 0) {
                $metrics[] = $sumReactions. ' Reaction(s)';
            }

            echo $repoReactionSummary->repoName .": ". implode('; ', $metrics) ."\n";
            if (count($repoReactionSummary->pullRequests) > 0) {
                $totalPrCount += count($repoReactionSummary->pullRequests);

                echo '  '. count($repoReactionSummary->pullRequests). ' Pull Request(s)'. "\n";
                foreach($repoReactionSummary->pullRequests as $pullRequest) {
                    echo '    #'. $pullRequest->number.' - '. $pullRequest->title;
                    echo "\n";
                }
            }

            if (count($repoReactionSummary->issueReactions) > 0) {
                $totalIssueCount += count($repoReactionSummary->issueReactions);
                $totalReactionsCount += $sumReactions;

                echo '  '.count($repoReactionSummary->issueReactions). ' Fixed Issue(s)'. "\n";
                foreach($repoReactionSummary->issueReactions as $issueReaction) {
                    echo '    #'. $issueReaction->number.' - '. $issueReaction->title;
                    if ($issueReaction->reactionsCount > 0) {
                        echo ' - '. $issueReaction->reactionsCount ." Reaction(s)";
                    }
                    echo "\n";
                }
            }
            echo "\n";

            $totalRepoCount++;
        }

        echo "\n\n";
        echo "@".$username ." contributed to ". $totalRepoCount ." open-source projects on github.com in ". $year ."\n";
        echo "  ". $totalPrCount ." merged Pull Request(s) - fixing ". $totalIssueCount ." reported Issue(s) - addressing ". $totalReactionsCount ." Reaction(s) \n";
    }
}