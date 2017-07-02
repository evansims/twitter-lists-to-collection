# twitter-lists-to-collection

This is a tiny PHP script that performs one simple task: it grabs tweets from your Lists and adds them to a Collection.

Collections are nifty but rather hidden feature that let you make curated timelines of tweets you pick and choose. If you've used Reddit's save feature with custom categories before, it works very similarly to that. Twitter has not yet integrated Collections into their website or mobile clients yet (except for viewing them, where they call them "timelines" for some reason? 🤔), so you need to use tools like [Tweetdeck](https://tweetdeck.twitter.com) or [Charms](http://charm.benapps.net/) to create or manage Collections.

There's a number of use cases for this, but I created it to aggregate the art lists I've created into one easily skimmable collection. I enable the 'media_only' filter to only collect tweets with media. It works great for quickly browsing art posted throughout the day, retweeting and liking along the way.

## Requirements
* A Twitter account.
* One or more Twitter Lists.
* A Twitter Collection.
* Somewhere to run this PHP script.

## Installation
1. Run 'composer install' to install the dependencies.
2. Copy 'state.default.json' to 'state.json'.
3. Update the 'state.json' files values as explained below.

## Configuration
The script's behavior is a configured using a simple JSON file, state.json.

``oauth_access_token``  
``oauth_access_token_secret``  
``consumer_key``  
``consumer_secret``  

Register your script as [an application with Twitter](https://apps.twitter.com/app/new). You need to ensure these have write access so the script can add things to your collection (the default is read only, I believe.) If you change the permissions you may need to regenerate these for it to reflect the change.

`username``

Your Twitter @username.

``media_only``

If you only want tweets with media attachments collected, set this to true.

``lists``

Specify the slugs (not names!) of the lists you want to collect from. The trailing value is updated by the script to keep track of the last tweet seen for API calls, so leave that at null for now.

For example if your List's URL is https://twitter.com/you/lists/whatever, then 'whatever' is the list's slug you'd want to use in your configuration.

``collection``

The ID of the collection you want tweets added to. You can find this by going to your collection's URL and looking at the url. If your collection's URL is https://twitter.com/you/timelines/12345, then 12345 is what you'd use in your configuration.
