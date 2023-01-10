<?php

namespace wsydney76\favorites\assets;

use Craft;
use craft\web\AssetBundle;

/**
 * Favorites Assets asset bundle
 */
class FavoritesAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';
    public $depends = [];
    public $js = ['scripts.js'];
    public $css = ['styles.css'];
}
