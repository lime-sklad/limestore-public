<?php

namespace Core\Classes\Utils;

class Stories
{
    public static function getStories()
    {
        $token = '323030333639363632373a414148557a67433454476b4b4b783267753076347159594f63785955467134525f534d';

        $token = hex2bin($token);

        $apiUrl = "https://api.telegram.org/bot$token/getUpdates";
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        $res = [];

        $resData = [];

        $message = '';
        $url = '';
        $type = '';

        if ($data['ok'] && !empty($data['result'])) {
            $resultArray = $data['result'];

            foreach ($resultArray as $key => $row) {

                //текстовое сообщение
                if (!empty($row['message']['text'])) {
                    $message = str_replace('/res ', '', $row['message']['text']) . '<br>';
                }

                $link_preview = !empty($row['message']['link_preview_options']) ?? false;

                if ($link_preview) {
                    if (!array_key_exists('is_disabled', $row['message']['link_preview_options'])) {
                        $url = $row["message"]["link_preview_options"]['url'];

                        $file_extension = pathinfo($url, PATHINFO_EXTENSION);

                        if ($file_extension == 'mp4') {
                            $type = 'video';
                        } else {
                            $type = 'image';
                        }
                    }
                }

                $resData[] = [
                    'message' => $message,
                    'url' => $url,
                    'type' => $type
                ];
            }
        }

        return $resData;
    }
}