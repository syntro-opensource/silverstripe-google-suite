<?php

namespace Syntro\SilverstripeGoogleSuite;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Config\Config as SSConfig;
use Syntro\SilverstripeKlaro\Config as KlaroConfig;
use Syntro\SilverstripeKlaro\KlaroRequirements;

/**
 * Class for managing the gtag.js requirement
 * @author Matthias Leutenegger
 */
class GTagConfig
{
    use Configurable;

    /**
     * A token to fetch the gtag script. not required, one will be taken from
     * Ads or analytics if empty
     * @config
     * @var string
     */
    private static $google_token = null;

    /**
     * the name of the default purpose
     * @config
     */
    private static $klaro_default_purpose = 'functional';

    /**
     * Add the created service to additional purposes. This service will be appended
     * to all purposes of the other tools.
     * @config
     */
    private static $klaro_purposes = [];

    /**
     * getGoogleToken - returns a google token to be used.
     *
     * @return string
     */
    public static function getGoogleToken()
    {
        $token = null;
        if ($ownToken = SSConfig::inst()->get(static::class, 'google_token')) {
            return $ownToken;
        } elseif (AnalyticsConfig::isEnabled() && count(AnalyticsConfig::getGoogleTokens()) > 0) {
            $token = AnalyticsConfig::getDefaultToken();
        } elseif (AdsConfig::isEnabled() && count(AdsConfig::getGoogleTokens()) > 0) {
            $token = AdsConfig::getDefaultToken();
        } else {
            throw new \Exception("No token available to load Global site tag!", 1);
        }
        return $token;
    }

    /**
     * isKlaroRequired - Check wether this service is required and cannot
     * be disabled.
     *
     * @return boolean
     */
    public static function isKlaroRequired()
    {
        $GARequired = AnalyticsConfig::isEnabled() && AnalyticsConfig::isKlaroRequired();
        $AWRequired = AdsConfig::isEnabled() && AdsConfig::isKlaroRequired();
        return $GARequired || $AWRequired;
    }

    /**
     * isKlaroEnabledByDefault
     *
     * @return boolean
     */
    public static function isKlaroEnabledByDefault()
    {
        $GAEnabled = AnalyticsConfig::isEnabled() && AnalyticsConfig::isKlaroEnabledByDefault();
        $AWEnabled = AdsConfig::isEnabled() && AdsConfig::isKlaroEnabledByDefault();
        return $GAEnabled || $AWEnabled;
    }

    /**
     * includeKlaroRequirements - adds the service and purpose to the klaro config
     *
     * @return void
     */
    public static function includeKlaroGlobalSiteTag()
    {
        $klaro_default_purpose = SSConfig::inst()->get(static::class, 'klaro_default_purpose');
        $klaro_purposes = SSConfig::inst()->get(static::class, 'klaro_purposes');
        $klaro_enabled_by_default = static::isKlaroEnabledByDefault();
        $klaro_required = static::isKlaroRequired();
        if ($klaro_default_purpose) {
            $klaro_purposes[] = $klaro_default_purpose;
        }



        SSConfig::modify()->merge(KlaroConfig::class, 'klaro_services', [
            'gtagjs' => [
                'title' => 'Google Global Site Tag',
                'description' => 'Allows Google services like Analytics and conversion tracking to work.',
                'default' => $klaro_enabled_by_default,
                'purposes' => $klaro_purposes,
                'required' => $klaro_required,
                'cookies' => [],//[ "/^_ga(_.*)?/" ],
            ]
        ]);
    }


    /**
     * includeGlobalSiteTag - adds the required global site tag
     *
     * @return void
     */
    public static function includeGlobalSiteTag()
    {
        $google_token = static::getGoogleToken();
        KlaroRequirements::klaroJavascript(
            "https://www.googletagmanager.com/gtag/js?id=$google_token",
            'gtagjs'
        );
        KlaroRequirements::customKlaroScript(
            <<<JS
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
            JS
            ,
            'gtagjs'
        );
    }
}
