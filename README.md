# Cache Flag v. 1.1.11 plugin for Craft CMS

The native ```{% cache %}``` tag is great, but in some cases the element queries Craft creates to clear the caches can become too complex, which can bog down your system. Cache Flag provides an alternative (and in most cases, more performant) way to have your caches clear automatically when your content changes.  

Looking for the Craft 3 version? It's [right here](https://github.com/mmikkel/CacheFlag-Craft3)!

## What does it do?

Cache Tag adds the ```{% cacheflag %}``` tag to Twig, which – like P&T's [Cold Cache plugin](https://github.com/pixelandtonic/ColdCache) – _doesn't create element queries_ for automatic cache breaking. Instead, Cache Flag gives you granular control over when a particular cache should be cleared, by adding _flags_ to content and caches.

Cache Flag draws inspiration from the excellent [CE Cache](http://www.causingeffect.com/software/expressionengine/ce-cache) plugin for ExpressionEngine, which implements _tags_ in a similar manner.

## How does it work?

The basic concept is that you add one or more flags (basically just strings, could be anything) to your content (sections, category groups, element types, etc) and to your caches (using the ```flagged``` parameter for the ```{% cacheflag %}``` tag). Whenever an element is saved or deleted, Cache Flag clears any caches with matching flags. Simple!

## Full usage example

Let's say you have section called "Awesome Stuff", and there's a cache that you want to clear every time content in that section changes. First, you add the flag ```awesome``` to the Awesome Stuff section in Cache Flag. Then, you flag the cache(s) you want to clear with ```awesome``` in your template, using Cache Flag's ```flagged``` parameter:

```jinja
{% cacheflag flagged "awesome" %}
    ...
{% endcacheflag %}
```

Now, whenever an entry in the Awesome Stuff section is saved or deleted, the above cache will be cleared.

Suppose you also want to have the above cache cleared whenever a _category_ in a particular category group is published or deleted. You could add the flag ```awesome``` to the relevant category group as well, or you could add another flag to it entirely, e.g. ```radical```. You can use a pipe delimiter to specify multiple flags in your template:


```jinja
{% cacheflag flagged "awesome|radical" %}
    ...
{% endcacheflag %}
```


Beyond the ```flagged``` parameter, the ```{% cacheflag %}``` tag _supports all the same parameters_ as the native ```{% cache %}``` tag – so I'll just refer to [the official documentation for the latter](http://buildwithcraft.com/docs/templating/cache).

### Events

Cache Flag dispatches two events:

* `cacheFlag.beforeDeleteFlaggedCaches`  

Dispatches just before Cache Flag deletes one or several template caches by flag.  

Event parameters: `flags` (array of flags having caches deleted) and `ids` (the IDs of all the templatecaches being deleted)   

* `cacheFlag.deleteFlaggedCaches`  

Dispatches immediately after Cache Flag has deleted one or several template caches by flag.  

Event parameters: `flags` (array of flags having caches deleted), `ids` (the IDs of all the templatecaches being deleted) and `result` (either `false` or the number of rows affected in `craft_templatecaches`).  

#### Listening to events

Listening to the Cache Flag events work as you'd expect:  

```php
craft()->on('cacheFlag.deleteFlaggedCaches', [$this, 'onDeleteFlaggedCaches']);
...

public function onDeleteFlaggedCaches(Event $event)
{
    $flags = $event->params['flags'];
    
    // ...custom logic, e.g. cache warming for the affected flags etc
}
```


### Changelog`

#### Version 1.1.11 – 03.04.18

* Fixes an issue where custom element types might return the wrong element type

#### Version 1.1.10 – 12.01.17

* Adds `cacheFlag.beforeDeleteFlaggedCaches` and `cacheFlag.deleteFlaggedCaches` events

#### Version 1.1.9 – 10.30.17

* Fixes a Live Preview related issue – thanks @michaelramuta!

#### Version 1.1.8 – 10.12.17

* Fixes an issue where programmatically busting the cache using console commands or non-CP HTTP requests was impossible

#### Version 1.1.7 – 10.11.17

* _Really_ fixes the regression error introduced in CacheFlag 1.1.5

#### Version 1.1.6 – 10.10.17

* Fixes a regression error introduced in CacheFlag 1.1.5 – thanks @aelvan!

#### Version 1.1.5 – 10.10.17

* Fixes an issue where CacheFlag would potentially create a lot of database queries when a keyed, non-global template cache was made global

#### Version 1.1.4 – 06.23.17

* Fixes an issue where clearing individual flags would fail when CSRF was enabled (thanks @aelvan)
* Fixes an issue w/ Live Preview (thanks @mjatharvest

#### Version 1.1.3 – 05.23.16

* Added option to clear individual flagged caches from CP section

#### Version 1.1.2 – 05.23.16

* Caches are now cleared for elements that have their status changed via element indexes

#### Version 1.1.1 – 05.05.16

* Fixed error where elementType flags was not being saved (thanks André)

#### Version 1.1.0 – 04.06.16

* The CP section now has a single Save button, and uses AJAX
* Empty flags are now deleted from the database
* Flagged caches using deleted flags are now cleared
* Fixed an issue where Cache Flag would create duplicate caches
* Fixed an issue where Cache Flag would not save flags where CSRF were enabled
* Fixed issue #2, where caches would fail to clear due to a typo
* Fixed issue #3, where Cache Flags CP section would choke on Craft Personal & Client

#### Version 1.0.4 - 12.11.15

* Fixed an issue where saving a global set wouldnt break flagged caches

#### Version 1.0.3 - 12.08.15

* Fixed a breaking bug resulting from a typo (thanks André Elvan!)
* Added Craft 2.5 features (release feed etc)

#### Version 1.0.2 - 10.21.15

* Fixed an issue w/ blank uid, dateCreated, dateUpdated columns for flagged caches
* Fixed several issues in Twig parser #1

#### Version 1.0.1 - 09.28.15

* Fixed an issue w/ wrong name for CacheFlagService

#### Version 1.0 - 09.26.15

* Initial public release


### Roadmap

Stay tuned for upcoming features.

* Events and hooks for cache warming etc.
* Varnish support


### Disclaimer

Cache Flag is provided free of charge. The author is not responsible for any data loss or other problems resulting from the use of this plugin.
Please report any bugs, feature requests or other issues [here](https://github.com/mmikkel/CacheFlag-Craft/issues). As Cache Flag is a hobby project, no promises are made regarding response time, feature implementations or bug amendments.
*Pull requests are very welcome!*

###
