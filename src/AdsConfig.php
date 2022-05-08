<?php
namespace Syntro\SilverstripeGoogleSuite;

use SilverStripe\Core\Config\Config as SSConfig;
use Syntro\SilverstripeKlaro\Config as KlaroConfig;
use Syntro\SilverstripeKlaro\KlaroRequirements;

/**
 * A Config interface for inserting Google ads conversion tracking in a
 * customizeable way.
 * @author Matthias Leutenegger
 */
class AdsConfig extends Config
{

    /**
     * @config
     * @var array
     */
    private static $onclick_conversion = [];

    /**
     * @config
     * @var array
     */
    private static $onsubmit_conversion = [];


    /**
     * getOnClickTrack
     *
     * @return array
     */
    public static function getOnClickTrack()
    {
        return SSConfig::inst()->get(static::class, 'onclick_conversion');
    }

    /**
     * getOnSubmitTrack
     *
     * @return array
     */
    public static function getOnSubmitTrack()
    {
        return SSConfig::inst()->get(static::class, 'onsubmit_conversion');
    }

    /**
     * includeKlaroRequirements - adds the service and purpose to the klaro config
     *
     * @return void
     */
    public static function includeKlaroRequirements()
    {
        $klaro_create_default_purpose = SSConfig::inst()->get(static::class, 'klaro_create_default_purpose');
        $klaro_purposes = SSConfig::inst()->get(static::class, 'klaro_purposes');
        $klaro_enabled_by_default = SSConfig::inst()->get(static::class, 'klaro_enabled_by_default');
        $klaro_opt_out = SSConfig::inst()->get(static::class, 'klaro_opt_out');

        if ($klaro_create_default_purpose) {
            SSConfig::modify()->merge(KlaroConfig::class, 'klaro_purposes', [
                'marketing' => ['title' => 'Marketing', 'description' => 'Tools used to manage and display ads']
            ]);
            $klaro_purposes[] = 'marketing';
        }


        SSConfig::modify()->merge(KlaroConfig::class, 'klaro_services', [
            'googleadstracking' => [
                'title' => 'Google Ads Conversion Tracking',
                'description' => 'Conversion tracking by Google',
                'default' => $klaro_enabled_by_default,
                'purposes' => $klaro_purposes,
                'cookies' => [ "/^_gc(_.*)?/" ],
                'extDependsOn' => ['gtagjs']
            ]
        ]);
    }

    /**
     * requireAnalytics - Loads the required Code
     *
     * @return void
     */
    public static function includeFrontendRequirements()
    {
        $script = static::getGTagConfigScript();
        $onClickTrack = json_encode(static::getOnClickTrack());
        $onSubmitTrack = json_encode(static::getOnSubmitTrack());
        $defaultToken = static::getDefaultToken();
        KlaroRequirements::customKlaroScript($script, 'googleadstracking');
        KlaroRequirements::customKlaroScript(
            <<<JS
                window.ssgsuiteOnClick = $onClickTrack;
                window.ssgsuiteOnSubmit = $onSubmitTrack;
                window.ssgsuiteDefaultToken = "$defaultToken";
            JS,
            //     window.ssgsuiteBroadcast = function (label, url, id = $defaultToken) {
            //         if (typeof(label) != 'string') {
            //             console.error('no conversion label given');
            //         }
            //         var callback = function () {
            //             if (typeof(url) != 'undefined') {
            //                 window.location = url;
            //             }
            //         };
            //         gtag('event', 'conversion', {
            //             'send_to': id + '/' + label,
            //             'event_callback': callback
            //         });
            //         return false;
            //     }
            // JS,
            'googleadstracking'
        );
        KlaroRequirements::klaroJavascript('syntro/silverstripe-google-suite:client/dist/aconvt.js', 'googleadstracking');
    }
}
