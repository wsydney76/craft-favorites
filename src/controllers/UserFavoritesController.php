<?php

namespace wsydney76\favorites\controllers;

use Craft;
use craft\elements\Entry;
use craft\web\Controller;
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
        return $this->asJson($this->getData());
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

        return $this->asJson($this->getData());
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


        return $this->asJson($this->getData());
    }


    protected function getData(string $message = ''): array
    {
        $currentUser = Craft::$app->user->identity;

        if (!$currentUser) {
            return [
                'loggedIn' => false,
                'ids' => [],
                'message' => ''
            ];
        }

        $ids = UserToEntries::find()
            ->select('entryId')
            ->where(['userId' => $currentUser->id])
            ->column()
        ;

        return [
            'loggedIn' => true,
            'ids' => $ids,
            'message' => $message
        ];
    }
}
