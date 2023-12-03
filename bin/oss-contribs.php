<?php

/*
 * (c) Markus Staab <markus.staab@redaxo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * https://github.com/staabm/oss-contribs
 */

use Github\Client;
use Symfony\Component\HttpClient\HttplugClient;

// graphql cheat sheet at https://medium.com/@tharshita13/github-graphql-api-cheatsheet-38e916fe76a3
// examples at https://gist.github.com/MichaelCurrin/f8a7a11451ce4ec055d41000c915b595#resources

$jsonString = file_get_contents('auth.json');
if (!$jsonString) {
    throw new \RuntimeException('auth.json not found');
}
$authData = json_decode($jsonString, true);
if (!$authData) {
    throw new \RuntimeException('Unable to json-decode auth.json');
}
if (!isset($authData['username'])) {
    throw new \RuntimeException('missing "username" in auth.json');
}
if (!isset($authData['token'])) {
    throw new \RuntimeException('missing "token" in auth.json');
}

$client = Client::createWithHttpClient(new HttplugClient());
$client->authenticate($authData['token'], Github\AuthMethod::ACCESS_TOKEN);
$graphql = $client->graphql();

$pullRequestFilter = new \staabm\OssContribs\PullRequest\PullRequestFilter($client);
$pullRequests = $pullRequestFilter->search("is:pr is:public is:merged author:". $authData['username'] ." created:>2023-01-01");

$reactionsFilter = new \staabm\OssContribs\PullRequest\SummaryBuilder($client);
$contribSummary = $reactionsFilter->build($pullRequests);



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
        echo '  '. count($repoReactionSummary->pullRequests). ' Pull Request(s)'. "\n";
        foreach($repoReactionSummary->pullRequests as $pullRequest) {
            echo '    #'. $pullRequest->number.' - '. $pullRequest->title;
            echo "\n";
        }
    }

    if (count($repoReactionSummary->issueReactions) > 0) {
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
}