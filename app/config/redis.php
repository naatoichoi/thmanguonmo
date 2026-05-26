<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Predis\Client;

class RedisConnection
{
    private static $client = null;

    public static function getConnection()
    {
        if (self::$client === null) {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
        }

        return self::$client;
    }
}