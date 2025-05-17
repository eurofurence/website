<?php
# Eurofurence Partner Links Update script
# to be called periodically, e.g. with
# 0 9 * * *     curl https://www.eurofurence.org/EF27/updatepartners.php > /dev/null

echo '<pre>';

$data_file = 'config/partners.json';

$data = json_decode(file_get_contents($data_file), true);
$new_data = $data; // since $data is an array, this is a copy

$ch = curl_init();

$now = time();

curl_setopt_array
(
    $ch,
    [
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => true
    ]
);

foreach ($data['conventions'] as $key => $con)
{
    echo "\n\n----- " . $key . " -----";

    $msg = "";

    // prepare data fields on new entries
    if (!array_key_exists('data', $con))
    {
        $con['data'] = $new_data['conventions'][$key]['data'] =
        [
            'timestamp' => $now,
            'status' => 0,
            'hash' => ""
        ];
    }

    // download banner
    if (!empty($con['source']))
    {
        // The source url better ends in a simple extension, not something like .png?w=200&h=80, or else
        // we'll need to utilize parse_url(), which does not provide simple access to file extensions.
        $destination = str_replace('{key}', $key, $data['path']) . '.' . pathinfo($con['source'])['extension'];

        echo "\nsource      : {$con['source']}";
        echo "\ndestination : {$destination}";

        curl_setopt($ch, CURLOPT_URL, $con['source']);

        $payload = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
    else
    {
        $payload = null;
        $status = -1;
    }


    $timestamp = $con['data']['timestamp'];
    $hash = md5($payload);

    // check for updates
    if ($hash !== $con['data']['hash'])
    {
        $new_data['conventions'][$key]['data']['hash'] = $hash;
        $new_data['conventions'][$key]['data']['timestamp'] = $now;
    }
    
    // check if last update is older than 365 days    
    if ($status > 0 && $now - $timestamp > 31536000)
    {
        $msg .= "\nLast update was seen " . round(($now - $timestamp) / 86400) . " days ago, disabling entry.";
    }
    
    echo "\nstatus      : {$status}";
    echo "\ntimestamp   : {$timestamp}";
    echo "\nage         : " . round(($now - $timestamp) / 86400) . " days";
    echo "\nhash        : {$hash}";

    if ($status > 0 && $status < 400)
    {
        file_put_contents($destination, $payload);
        $new_data['conventions'][$key]['file'] = $destination;
    }
    else if ($status === 0)
    {
        $msg .= "\nNo source url, skipping status assessment.";
    }
    else
    {
        $msg .= "\nError downloading file, disabling entry.";
        $new_data['conventions'][$key]['enable'] = false;
    }

    echo "\nenabled     : " . boolstr($con['enable']) . " -> " . boolstr($new_data['conventions'][$key]['enable']);

    // copy data over to new data instance
    $new_data['conventions'][$key]['data']['status'] = $status;
    $new_data['conventions'][$key]['data']['timestamp'] = $timestamp;
    $new_data['conventions'][$key]['data']['hash'] = $hash;

    if (!empty($msg))
    {
        echo $msg;
    }
}

echo '</pre>';

file_put_contents($data_file, json_encode($new_data, JSON_PRETTY_PRINT));

function boolstr($bool)
{
    return $bool ? "✅" : "🟥";
}
