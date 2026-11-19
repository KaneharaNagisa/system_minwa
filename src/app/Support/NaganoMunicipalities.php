<?php

namespace App\Support;

use JsonException;
use RuntimeException;

class NaganoMunicipalities
{
    /**
     * @return list<array{name: string, region_name: string}>
     *
     * @throws JsonException
     */
    public static function all(): array
    {
        $contents = file_get_contents(resource_path('data/nagano-municipalities.json'));

        if ($contents === false) {
            throw new RuntimeException('長野県市町村データを読み込めませんでした。');
        }

        $mapData = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        $regions = [
            '北信' => [
                '長野市',
                '須坂市',
                '中野市',
                '飯山市',
                '千曲市',
                '栄村',
                '野沢温泉村',
                '山ノ内町',
                '木島平村',
                '小布施町',
                '高山村',
                '信濃町',
                '小川村',
                '飯綱町',
            ],
            '東信' => [
                '上田市',
                '小諸市',
                '佐久市',
                '東御市',
                '小海町',
                '川上村',
                '南牧村',
                '南相木村',
                '北相木村',
                '佐久穂町',
                '軽井沢町',
                '御代田町',
                '立科町',
                '青木村',
                '長和町',
                '坂城町',
            ],
            '南信' => [
                '岡谷市',
                '飯田市',
                '諏訪市',
                '伊那市',
                '駒ヶ根市',
                '茅野市',
                '下諏訪町',
                '富士見町',
                '原村',
                '辰野町',
                '箕輪町',
                '飯島町',
                '南箕輪村',
                '中川村',
                '宮田村',
                '松川町',
                '高森町',
                '阿南町',
                '阿智村',
                '平谷村',
                '根羽村',
                '下條村',
                '売木村',
                '天龍村',
                '泰阜村',
                '喬木村',
                '豊丘村',
                '大鹿村',
            ],
        ];
        $regionByName = [];

        foreach ($regions as $regionName => $municipalities) {
            foreach ($municipalities as $municipalityName) {
                $regionByName[$municipalityName] = $regionName;
            }
        }

        return array_map(
            fn (array $municipality): array => [
                'name' => $municipality['name'],
                'region_name' => $regionByName[$municipality['name']] ?? '中信',
            ],
            $mapData,
        );
    }
}
