## Deployment

### Шаг 1

```shell
composer install
```

### Шаг 2

```shell
./vendor/bin/sail up
```

### Шаг 3

```shell
php artisan migrate --seed
```

### Шаг 3 DOCKER

```shell
./vendor/bin/sail artisan migrate --seed
```

### Документация

###### Маршрут

```text
/docs/api/
```

### Смена версии API

Версия API переключается через реализацию `TaskControllerActionsInterface`.

Текущая реализация привязана в `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
$this->app->bind(TaskControllerActionsInterface::class, TaskControllerActions::class);
```

Чтобы сменить версию API:

Создайте новую реализацию интерфейса:

```php
// app/Actions/TaskControllerActionsV2.php
class TaskControllerActionsV2 implements TaskControllerActionsInterface
{
    // новая логика
}
```

Измените бинд в `AppServiceProvider`:

```php
$this->app->bind(TaskControllerActionsInterface::class, TaskControllerActionsV2::class);
```

Контроллер `TaskController` зависит от интерфейса, поэтому смена реализации автоматически меняет поведение всех эндпоинтов.
