# Favorites

Let a user select favorite entries.

Work in progress.

Another proof-of-concept for decoupling and modernizing existing functionality.

## Requirements

This plugin requires Craft CMS 4.3.5 or later, and PHP 8.0.2 or later.

Requires [Alpine.js](https://alpinejs.dev/essentials/installation).

## Installation

Update `composer.json`

```json
{
  "minimum-stability": "dev",
  "prefer-stable": true,
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/wsydney76/craft-favorites"
    }
  ]
}
```

### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require wsydney76/craft-favorites

# tell Craft to install the plugin
./craft plugin/install favorites
```

## Usage

### Show icon

Users can click on this icon to select/remove an entry.

```twig
{% if craft.app.plugins.pluginEnabled('favorites') %}
    {% include '@favorites/_favorite.twig' with {
        entry
    } only %}
{% endif %}
```

It is safe to use this on cached pages.

### Retrieve favorites

```twig
{% set ids = craft.favorites.ids %}

{% set entries = craft.favorites.entries('postDate desc') %}
```

```php
use wsydney76\favorites\Plugin;

...

$entries = Plugin::getInstance()->favoritesService->getEntries();

$ids = Plugin::getInstance()->favoritesService->getIds();
```

Note: Obviosly this will not on cached pages.

See API.

## Settings

### Star Color 

Change the color of the star icons in the plugin settings.

### Load Assets

Whether the plugin should load its own JavaScript and CSS assets.

True by default, this will add additional requests to your site (1x js, 1x css).

If set to false, your project must include them in to its own asset bundle.

Assets live in `vendor/wsydney76/craft-favorites/src/assets/dist`, they don't need a build step.

## API

All calls must set a 'accepts json' header, e.g.

```javascript
fetch(url, {
    headers: {
        'Accept': 'application/json'
    }
})
```

`/actions/favorites/user-favorites/get` 

`/actions/favorites/user-favorites/add?id=4711`

`/actions/favorites/user-favorites/remove?id=4711`

These calls will return a JSON response formatted via Craft's `->asModelSuccess`, where the `ids` key
will contain all ids in the list for the current user after the `add/remove` action was performed.

```json
{
  "modelName": "favorites",
  "favorites": {
    "loggedIn": true,
    "ids": [
      846,
      1061
    ]
  },
  "message": "Added to favorites"
}
```

### Retrieve entries


`/actions/favorites/user-favorites/get-entries?orderBy=postDate`

This will return an array as JSON response with basic fields (title, url, section)

Calling this from a front end component is safe to use on cached pages.

In its simplest form as Alpine.js component:

```javascript
<div x-data="{
    entries: [],
    async init() {
        const response = await fetch('{{ currentSite.baseUrl }}actions/favorites/user-favorites/get-entries?orderBy=postDate', {
            headers: {
                'Accept': 'application/json'
            }
        })
        this.entries = await response.json()
    }}">

    <template x-for="entry in entries">
        <div>
            <a :href="entry.url" x-text="entry.title"></a>
        </div>
    </template>

</div>
```

## Roadmap

* Use local storage for guests.
* Customizable strings.