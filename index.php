<?php

require('vendor/autoload.php');

$state = null;
$originalStateMD5 = null;

if (! file_exists('state.json')) {
  echo "Please copy state.default.json to state.json and update the values accordingly.";
  exit;
}

if (file_exists('state.json')) {
  $originalStateMD5 = md5(file_get_contents('state.json'));
  $state = json_decode(file_get_contents('state.json'), true);
}

$queue = [];

foreach($state['lists'] as $listSlug => $listSince) {
  $listSince = ($listSince ? '&since_id=' . $listSince : null);

  $twitter = newRequest();
  $twitter->setGetfield('?owner_screen_name=' . $state['username'] . '&count=100&slug=' . $listSlug . $listSince);
  $twitter->buildOauth('https://api.twitter.com/1.1/lists/statuses.json', 'GET');

  $tweets = json_decode($twitter->performRequest(), true);

  if (count($tweets)) {
    $state['lists'][$listSlug] = $tweets[0]['id_str'];

    foreach ($tweets as $tweet) {
      if (isset($tweet['retweeted']) && $tweet['retweeted'] === true) {
        continue;
      }

      if ($state['media_only'] === true) {
        if (isset($tweet['entities']) && isset($tweet['entities']['media'])) {
          if (isset($tweet['retweeted_status'])) {
            $queue[] = $tweet['retweeted_status']['id_str'];
            continue;
          }

          $queue[] = $tweet['id_str'];
        }

        continue;
      }

      if (isset($tweet['retweeted_status'])) {
        $queue[] = $tweet['retweeted_status']['id_str'];
        continue;
      }

      $queue[] = $tweet['id_str'];
    }

    $queue = array_unique($queue);
  }
}

if (count($queue)) {
  foreach($queue as $tweet) {
    $twitter = newRequest();
    $twitter->buildOauth('https://api.twitter.com/1.1/collections/entries/add.json', 'POST')
            ->setPostfields(['id' => 'custom-' . $state['collection'], 'tweet_id' => (string)$tweet])
            ->performRequest();
  }
}

complete();

function newRequest() {
  global $state;

  return new TwitterAPIExchange([
    'oauth_access_token'        => $state['oauth_access_token'],
    'oauth_access_token_secret' => $state['oauth_access_token_secret'],
    'consumer_key'              => $state['consumer_key'],
    'consumer_secret'           => $state['consumer_secret']
  ]);
}

function complete() {
  global $state, $originalStateMD5;

  $state = json_encode($state, JSON_PRETTY_PRINT);
  $newStateMD5 = md5($state);

  if ($newStateMD5 !== $originalStateMD5) {
    file_put_contents('state.json', $state);
  }

  exit;
}
