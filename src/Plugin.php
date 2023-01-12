<?php

namespace wsydney76\favorites;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\App;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use wsydney76\favorites\models\Settings;
use wsydney76\favorites\services\FavoritesService;
use yii\base\Event;
use wsydney76\favorites\assets\FavoritesAsset;

/**
 * Favorites plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @author wsydney76 <wsydney@web.de>
 * @copyright wsydney76
 * @license MIT
 * @property-read FavoritesService $favoritesService
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => ['favoritesService' => FavoritesService::class],
        ];
    }

    public function init()
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('favorites/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        if ($this->getSettings()->loadAssets) {
            Craft::$app->view->registerAssetBundle(FavoritesAsset::class);
        }

        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['@favorites'] = $this->getBasePath() . '/templates';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $e) {
                /** @var CraftVariable $variable */
                $variable = $e->sender;

                $variable->set('favorites', FavoritesService::class);
            }
        );

    }
}
