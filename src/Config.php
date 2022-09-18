<?php

namespace Syntro\SilverstripeGoogleSuite;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Config\Config as SSConfig;

/**
 * base class for each google product config. Manages tokens and services
 * @author Matthias Leutenegger
 */
abstract class Config
{
    use Configurable;

    /**
     * if true, this product is actively loaded into the page
     * @config
     * @var bool
     */
    private static $is_enabled = true;

    /**
     * A set of tokens used to communicate with google
     * @config
     * @var array
     */
    private static $google_tokens = [];

    /**
     * if true, a new purpose will be created
     * @config
     */
    private static $klaro_create_default_purpose = true;

    /**
     * the name of the default purpose
     * @config
     * @var string
     */
    private static $klaro_default_purpose = 'functional';

    /**
     * Add the created service to additional purposes. If 'klaro_create_default_purpose'
     * is true, the default purpose will be appended.
     * @config
     */
    private static $klaro_purposes = [];

    /**
     * if true, the generated service will be enabled by default
     * WARNING: enabling this will most likely violate GDPR rules
     * @config
     */
    private static $klaro_enabled_by_default = false;

    /**
     * if true, the generated service will be required
     * WARNING: enabling this will most likely violate GDPR rules
     * @config
     */
    private static $klaro_required = false;

    /**
     * isEnabled
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return
            SSConfig::inst()->get(static::class, 'is_enabled') &&
            count(static::getGoogleTokens()) > 0;
    }

    /**
     * getKlaroPurposes
     *
     * @return array
     */
    public static function getKlaroPurposes()
    {
        $klaro_create_default_purpose = SSConfig::inst()->get(static::class, 'klaro_create_default_purpose');
        $klaro_purposes = SSConfig::inst()->get(static::class, 'klaro_purposes');
        $klaro_default_purpose = SSConfig::inst()->get(static::class, 'klaro_default_purpose');
        if ($klaro_create_default_purpose) {
            $klaro_purposes[] = $klaro_default_purpose;
        }
        return $klaro_purposes;
    }

    /**
     * getGoogleTokens - fetches the tokens
     *
     * @return array
     */
    public static function getGoogleTokens()
    {
        return SSConfig::inst()->get(static::class, 'google_tokens');
    }

    /**
     * getGoogleTokens - fetches the tokens
     *
     * @return string
     */
    public static function getDefaultToken()
    {
        $tokens = SSConfig::inst()->get(static::class, 'google_tokens');
        if (count($tokens) > 0) {
            $token = $tokens[0];
            if (is_array($token)) {
                return $token['token'];
            }
            return $token;
        }
        throw new \Exception("Can not get a default token when no tokens are configured for " . __CLASS__, 1);
    }

    /**
     * isKlaroRequired - check if this service is a required one
     *
     * @return boolean
     */
    public static function isKlaroRequired()
    {
        return SSConfig::inst()->get(static::class, 'klaro_required');
    }

    /**
     * isKlaroEnabledByDefault
     *
     * @return boolean
     */
    public static function isKlaroEnabledByDefault()
    {
        return SSConfig::inst()->get(static::class, 'klaro_enabled_by_default') || static::isKlaroRequired();
    }

    /**
     * getGTagConfigScript
     *
     * @return string
     */
    public static function getGTagConfigScript()
    {
        $google_tokens = static::getGoogleTokens();
        $script = '';
        foreach ($google_tokens as $token) {
            if (is_array($token)) {
                $tokenString = $token['token'];
                unset($token['token']);
                unset($token['title']);
                if (count($token) > 0) {
                    $options = json_encode($token);
                    $script .= "gtag('config', '$tokenString', $options);";
                } else {
                    $script .= "gtag('config', '$tokenString');";
                }
            } else {
                $script .= "gtag('config', '$token');";
            }
        }
        return $script;
    }

    /**
     * includeKlaroRequirements - adds the service and purpose to the klaro config
     *
     * @return void
     */
    abstract public static function includeKlaroRequirements();


    /**
     * includeFrontendRequirements - adds the required frontend requirements using
     * the KlaroRequirements way
     *
     * @return void
     */
    abstract public static function includeFrontendRequirements();
}
