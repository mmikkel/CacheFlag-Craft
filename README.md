# Cache Flag v. 1.0.2 plugin for Craft CMS

The native ```{% cache %}``` tag is great, but in some cases the element queries Craft creates to clear the caches can become too complex, which can bog down your system. Cache Flag provides an alternative (and in most cases, more performant) way to have your caches clear automatically when your content changes.

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


### Changelog

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