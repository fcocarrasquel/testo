<?php

namespace BeycanPress\Tokenico\Services;

use \BeycanPress\Tokenico\PluginHero\Helpers;
use \BeycanPress\Tokenico\PluginHero\Plugin;

class Contract
{
    use Helpers;

    /**
     *
     * @var array
     */
    private static $versions = [
        "TokenICO" => 'v1'
    ];

    /**
     *
     * @var object|null
     */
    private static $factories = null;

    /**
     * @param string $contract
     * @return string
     */
    public static function getVersion(string $contract) : string
    {
        if (!$contract) return self::$versions['TokenICO'];

        return (string) self::$versions[$contract];
    }

    /**
     * @return object
     */
    public static function getVersions() : object
    {
        return (object) self::$versions;
    }

    /**
     * @param string $contract
     * @param string $version
     * @return array
     */
    public static function getAbi(string $contract, string $version) : array
    {
        self::getFactories();

        if (!$contract) return self::$factories->TokenICO->v1;

        return self::$factories->$contract->$version;
    }

    /**
     * @return object
     */
    public static function getFactories() : object
    {
        if (is_null(self::$factories)) {
            self::$factories = json_decode(file_get_contents(Plugin::$instance->pluginDir . 'resources/contract-factories.json'));
        }

        return (object) self::$factories;
    }
}