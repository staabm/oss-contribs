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
$pullRequests = $pullRequestFilter->search("is:pr is:public is:merged author:staabm created:>2023-10-01");

$reactionsFilter = new \staabm\OssContribs\PullRequest\ReactionsFilter($client);
$contribSummary = $reactionsFilter->search($pullRequests);



foreach($contribSummary->repositoryReactionSummaries as $repoReactionSummary) {
    echo $repoReactionSummary->repoName ."\n";

    foreach($repoReactionSummary->issueReactions as $issueReaction) {
        echo '  #'. $issueReaction->number.' - '. $issueReaction->title .' '. $issueReaction->reactionsCount ." Reactions\n";
    }
}