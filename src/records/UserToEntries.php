<?php

namespace wsydney76\favorites\records;

use Craft;
use craft\db\ActiveRecord;

/**
 * User To Entries record
 */
class UserToEntries extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%favorites_user_entries}}';
    }
}
