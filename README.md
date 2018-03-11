# Telegram Bots API

## Базовые возможности

  * Дефолтный бот для различных уведомлений с сайта
    * Хендлер уведомлений webform
    * Rules action
    * Core action
  * Плагин позволяющий добавить собственных ботов
    * Система команд https://telegram-bot-sdk.readme.io/docs/commands-system 
    
## Создание своего бота
  * Создать плагин `Drupal\MODULENAME\Plugin\TelegramBots\YourPluginName`
    * `id` - Id плагина может быть любым
    * `configurable = TRUE` - Плагин будет иметь свою форму настроек. Где можно указать токен, имя бота`_bot`    

## Детали 
 За webhook отвечает `telegram_bots_api\src\Controller\Webhook.php`
 