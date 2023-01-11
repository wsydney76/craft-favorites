<?php

namespace wsydney76\favorites\controllers;

use Craft;
use craft\elements\Entry;
use craft\web\Controller;
use wsydney76\favorites\models\Favorites;
use wsydney76\favorites\Plugin;
use wsydney76\favorites\records\UserToEntries;
use yii\web\Response;

/**
 * Favorites controller
 */
class UserFavoritesController extends Controller
{

    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    public function beforeAction($action): bool
    {
        $this->requireAcceptsJson();
        return parent::beforeAction($action);
    }


    public function actionGet(): Response
    {
        return $this->asModelSuccess($this->getData(), 'Init', 'favorites');
    }

    public function actionAdd(): Response
    {
        $this->requireLogin();

        $id = Craft::$app->request->getRequiredQueryParam('id');

        $currentUser = Craft::$app->user->identity;

        $criteria = [
            'userId' => $currentUser->id,
            'entryId' => $id
        ];

        if (!Entry::findOne($id)) {
            return $this->asJson($this->getData("Invalid entry id $id"));
        }

        if (UserToEntries::find()->where($criteria)->exists()) {
            return $this->asJson($this->getData());
        }

        $record = new UserToEntries($criteria);

        $record->save();

        return $this->asModelSuccess(
            $this->getData(),
            Craft::t('favorites', 'Added to favorites'),
            'favorites');
    }

    public function actionRemove(): Response
    {
        $this->requireLogin();

        $id = Craft::$app->request->getRequiredQueryParam('id');

        $currentUser = Craft::$app->user->identity;

        UserToEntries::deleteAll([
            'userId' => $currentUser->id,
            'entryId' => $id
        ]);

        return $this->asModelSuccess(
            $this->getData(),
            Craft::t('favorites', 'Removed from favorites'),
            'favorites');
    }

    public function actionGetEntries()
    {

        $orderBy = Craft::$app->request->getQueryParam('orderBy', 'title');
        $entries = Plugin::getInstance()->favoritesService->getEntries($orderBy);

        return $this->asJson($entries->map(fn(Entry $entry) => [
            'id' => $entry->id,
            'title' => $entry->title,
            'url' => $entry->url,
            'sectionHandle' => $entry->section->handle,
            'sectionName' => $entry->section->name,
            'typeHandle' => $entry->type->handle,
            'typeName' => $entry->type->name,
        ]));
    }


    protected function getData(string $message = ''): Favorites
    {
        $currentUser = Craft::$app->user->identity;

        if (!$currentUser) {
            return new Favorites();
        }

        $ids = Plugin::getInstance()->favoritesService->getIds();

        return new Favorites([
            'loggedIn' => true,
            'ids' => $ids
        ]);
    }
}
