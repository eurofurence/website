<?php declare(strict_types=1);
/**
 * Telegram Reporting Library 2.0
 * (c) 2025 draconigen@dogpixels.net
 * AGPL 3.0, see https://www.gnu.org/licenses/agpl-3.0.de.html
 * Provided "as is", without warranty of any kind.
 */

# configurable telegram config file path
define('TELEGRAM_CONFIG_PATH', 'telegram.config.php');

# create file if it doesn't exist
if (!file_exists(TELEGRAM_CONFIG_PATH)) {
    file_put_contents(TELEGRAM_CONFIG_PATH, "<?php\ndefine('TELEGRAM_BOT_API_TOKEN', '');\ndefine('TELEGRAM_TARGET_USERID', '');\n");
}

# load config file
include_once(TELEGRAM_CONFIG_PATH);

# check config file contents and raise exception if empty
if (empty(TELEGRAM_BOT_API_TOKEN) || empty(TELEGRAM_TARGET_USERID)) {
    throw new Exception("Telegram config missing in " . TELEGRAM_CONFIG_PATH);
}

class Telegram {
    public static function report(string $message, array $inlineKeyBoardButtons = []) {
        # prepare telegram api payload
        $payload = [
            'chat_id' => TELEGRAM_TARGET_USERID,
            'text' => sprintf("```\n%s\n```", htmlspecialchars_decode($message)),
            'parse_mode' => 'MarkdownV2'
        ];

        if (!empty($inlineKeyBoardButtons)) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => [$inlineKeyBoardButtons]
            ]);
        }

        # init & configure curl request
        $curl = curl_init("https://api.telegram.org/bot" . TELEGRAM_BOT_API_TOKEN . "/sendMessage");

        curl_setopt_array($curl, [
                CURLOPT_POST => true,                             # set POST request method
                CURLOPT_POSTFIELDS => http_build_query($payload), # attach url-encoded post data
                CURLOPT_RETURNTRANSFER => true                    # return response, rather than printing it to stdout
            ]
        );        

        # exec api call and check response
        $response = curl_exec($curl);
        if (!json_decode($response)->ok) {
            // throw new Exception("Telegram API Error:\n{$response}");
            return false;
        }
        
        return true;
    }
}