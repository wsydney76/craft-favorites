<?php

namespace wsydney76\favorites\services;

use Craft;
use craft\elements\Entry;
use Illuminate\Support\Collection;
use wsydney76\favorites\records\UserToEntries;
use yii\base\Component;

/**
 * Favorites Service service
 */
class FavoritesService extends Component
{
    public function getIds(): array
    {
        $currentUser = Craft::$app->user->identity;

        if (!$currentUser) {
            return [];
        }

        return UserToEntries::find()
            ->select('entryId')
            ->where(['userId' => $currentUser->id])
            ->column();
    }

    public function getEntries($orderBy = 'title'): Collection
    {
        return Entry::find()
            ->id($this->getIds())
            ->orderBy($orderBy)
            ->collect();
    }

}
