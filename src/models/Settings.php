<?php

namespace wsydney76\favorites\models;

use Craft;
use craft\base\Model;
use craft\validators\ColorValidator;

/**
 * Favorites settings
 */
class Settings extends Model
{
    public string $starColor = '#ca8a04';

    public function rules(): array
    {
        return [
            ['starColor', 'required'],
            ['starColor', ColorValidator::class]
        ];
    }
}
