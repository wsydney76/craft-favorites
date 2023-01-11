<?php

namespace wsydney76\favorites\models;

use Craft;
use craft\base\Model;
use craft\validators\ColorValidator;

/**
 * Favorites model
 */
class Favorites extends Model
{
    public bool $loggedIn = false;
    public array $ids = [];

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }
}
