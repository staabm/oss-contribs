<?php

/*
 * (c) Markus Staab <markus.staab@redaxo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * https://github.com/staabm/oss-contribs
 */

use Github\AuthMethod;
use Github\Client;
use staabm\OssContribs\CliHelper;
use staabm\OssContribs\ConsoleApplication;
use Symfony\Component\HttpClient\HttplugClient;

$paths = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../autoload.php',
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        include $path;
        break;
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');

$authFile = getcwd() .'/auth.json';
$jsonString = @file_get_contents($authFile);
if (!$jsonString) {
    CliHelper::setupAuth($authFile);

    $jsonString = @file_get_contents($authFile);
    if (!$jsonString) {
        throw new \RuntimeException('auth.json not found in '.$authFile);
    } else {
        echo $authFile .' successfully created.'.PHP_EOL;
    }
}
$authData = json_decode($jsonString, true);
if (!$authData) {
    throw new \RuntimeException('Unable to json-decode auth.json');
}
if (!isset($authData['token'])) {
    throw new \RuntimeException('missing "token" in auth.json');
}

$client = Client::createWithHttpClient(new HttplugClient());
$client->authenticate($authData['token'], AuthMethod::ACCESS_TOKEN);

$username = CliHelper::askUsername();
$year = CliHelper::askYear();

$app= new ConsoleApplication();
$app->run($client, $username, $year);