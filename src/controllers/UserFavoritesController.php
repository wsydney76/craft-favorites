<?php

namespace wsydney76\favorites\controllers;

use Craft;
use craft\elements\Entry;
use craft\web\Controller;
use wsydney76\favorites\models\Favorites;
use wsydney76\favorites\Plugin;
use wsydney76\favorites\records\UserToEntries;
use yii\base\InvalidArgumentException;
use yii\web\Response;
use function sleep;

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
