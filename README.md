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

## Settings

* Change the color of the star icons in the plugin settings.

## Roadmap

* Use local storage for guests.
* Customizable strings.