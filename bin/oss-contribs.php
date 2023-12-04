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
$client->authenticate($authData['token'], AuthMethod::ACCESS_TOKEN);

$app= new ConsoleApplication();
$app->run($client, $authData['username'], 2023);