# Smsc notifications channel for Laravel 5.3+

This package makes it easy to send notifications using ArubaSms with Laravel 5.3+.

## Contents

- [Installation](#installation)
    - [Setting up the SmscAruba service](#setting-up-the-SmscAruba-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require gresci/aruba-sms
```

Then you must install the service provider:
```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\SmscAruba\SmscArubaServiceProvider::class,
],
```

### Setting up the SmscAruba service

Add your SmscAruba login, secret key (hashed password) and default sender name (or phone number) to your `config/services.php`:

```php
// config/services.php
...
'smscaruba' => [
    'login'  => env('SMSARUBA_LOGIN'),
    'secret' => env('SMSARUBA_SECRET'),
    'sender' => 'John_Doe'
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\SmscAruba\SmscArubaMessage;
use NotificationChannels\SmscAruba\SmscArubaChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [SmscArubaChannel::class];
    }

    public function toSmscAruba($notifiable)
    {
        return SmscArubaMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a routeNotificationForSmsRu() method, which return the phone number.

```php
public function routeNotificationForSmsRu()
{
    return $this->phone;
}
```

### Available methods

`from()`: Sets the sender's name or phone number.

`content()`: Sets a content of the notification message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email jhaoda@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [JhaoDa](https://github.com/jhaoda)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
