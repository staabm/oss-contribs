<?php

namespace staabm\OssContribs\PullRequest;

use Github\Client;
use Iterator;

final class PullRequestFilter {
    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @return Iterator<PullRequest>
     */
    public function search(string $queryFilter): Iterator {
        $graphql = $this->client->graphql();

        $queryTemplate = <<<'GRAPHQL'
query {
  search(query: "%query%", type: ISSUE, first: 75 %cursor%) {
    edges {
      node {
        ... on PullRequest {
          url
          number
          title
          bodyText
        }
      }
    }
    pageInfo {
      startCursor
      hasNextPage
      endCursor
    }
  }
}
GRAPHQL;

        // "closingIssuesReferences" does not work for my use-cases
// see https://github.com/KnpLabs/php-github-api/issues/1128
        /*
                  closingIssuesReferences(first: 25) {
                    nodes {
                      number
                      url
                      reactions(first: 10) {
                        totalCount
                        nodes {
                          id
                          content
                        }
                      }
                    }
                  }
         */

        $cursor = '';
        do {
            $query = str_replace('%query%', $queryFilter, $queryTemplate);
            $query = str_replace('%cursor%', $cursor, $query);
            $result = $graphql->execute($query);

            if (!isset($result['data']['search']['edges'])) {
                return;
            }

            foreach ($result['data']['search']['edges'] as $edge) {
                $pr = $edge['node'];

                yield new PullRequest(
                    $pr['number'],
                    $pr['title'],
                    $pr['bodyText'],
                    $pr['url'],
                    KeywordsParser::findReferencedIssues($pr['url'], $pr['bodyText']),
                );
            }

            $cursor = ', after: "' . $result['data']['search']['pageInfo']['endCursor'] . '"';
        } while ($result['data']['search']['pageInfo']['hasNextPage']);
    }
}