<?php

namespace staabm\OssContribs;

final class CliHelper {
    public static function setupAuth(string $authFile)
    {
        $token = self::askApiToken();

        file_put_contents($authFile, json_encode(['token' => $token], JSON_PRETTY_PRINT));
    }

    static public function askYear(): int {
        while (true) {
            $answer = (int) self::ask('Enter the year you want to analyze (e.g. 2023):');

            if ($answer < 2000 || $answer > 2050) {
                echo 'Invalid. Please try again or CTRL+C to exit.'.PHP_EOL;
                continue;
            }

            return $answer;
        }
    }

    static public function askUsername(): string {
        while (true) {
            $answer = self::ask('Enter the GitHub username you want to analyze (e.g. staabm):');

            if (!self::isValidUsername($answer)) {
                echo 'Invalid. Please try again or CTRL+C to exit.'.PHP_EOL;

                continue;
            }

            return $answer;
        }
    }

    static private function askApiToken(): string {
        while (true) {
            $answer = self::ask('Enter a GitHub API access token:');

            if (!self::isValidApiToken($answer)) {
                echo 'Invalid. Please try again or CTRL+C to exit.'.PHP_EOL;

                continue;
            }

            return $answer;
        }
    }

    static private function isValidUsername(string $username): bool {
        return preg_match('/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}$/i', $username) == 1;
    }

    static private function isValidApiToken(string $token): bool {
        // validate token https://gist.github.com/magnetikonline/073afe7909ffdd6f10ef06a00bc3bc88
        if (preg_match('/^ghp_[a-zA-Z0-9]{36}$/i', $token) == 1) {
            return true;
        }

        if (preg_match('/^github_pat_[a-zA-Z0-9]{22}_[a-zA-Z0-9]{59}$/i', $token) == 1) {
            return true;
        }

        return false;
    }

    static private function ask(string $question): string {
        echo $question.' ';

        $handle = fopen("php://stdin","r");
        $answer = fgets($handle);
        fclose($handle);

        return trim($answer ?: '');
    }
}