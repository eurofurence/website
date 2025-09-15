# PHP Telegram Reporting

Provides a very simple way to have a Telegram bot report to a single account through the static class `Telegram`.

## Setup

You will need two things: a bot through which the reportings will be sent, and the *userID* of the user to receive these reports.

To create a bot, talk to [@BotFather](https://t.me/BotFather) on Telegram. You will receive a *token* in the process, which will be relevant soon.

Next, obtain e.g. your own *userID*. Some clients support showing it, e.g. the official Telegram for Web client. And then there are also bots that'll reply your UserID to you, when you start talking to them. Just google it, really.

## Usage

Then, download telegram.php and include it in your PHP script:

```PHP
include('telegram.php');
```

Note that upon running the script for the first time, a configuration file like `telegram.config.php` will appear next to the script, and execution will fail with an exception that the configuration must be completed.
Naturally, the process running the script will need write access to the directory the script resides in.

## Configuration

Configuration is mandatory and requires both the *token* and *userID* obtained during setup. After running the script for a first time after including it, open the newly appeared `telegram.config.php` and edit the provided fields:

`define('TELEGRAM_BOT_API_TOKEN', '');` Insert your bot's *token* here.
`define('TELEGRAM_TARGET_USERID', '');` Insert your *userID* here.

## Reporting

Now that everything is set up, you can call `Telegram::report(string $message)` to have your bot report any string to the user specified via *userID*:

```PHP
try {
    Telegram::report("Hello World");
}
catch (Exception $ex) {
    var_dump($ex);
}
```

> Whenever an operation fails, an exception will be thrown with additional information. Make sure to wrap every call in a `try-else` block.

## License

This library is licensed under the terms of the *GNU Affero General Public License 3.0*,
Copyright draconigen@dogpixels.net.
See the LICENSE file or https://www.gnu.org/licenses/agpl-3.0.en.html for full details.