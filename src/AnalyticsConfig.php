<?php
namespace Syntro\SilverstripeGoogleSuite;

use SilverStripe\Core\Config\Config as SSConfig;
use Syntro\SilverstripeKlaro\Config as KlaroConfig;
use Syntro\SilverstripeKlaro\KlaroRequirements;

/**
 * A Config interface for inserting the google analytics snippet in a
 * customizeable way.
 * @author Matthias Leutenegger
 */
class AnalyticsConfig  extends Config
{
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
                'analytics' => ['title' => 'Analytics', 'description' => 'Tools used to gather usage statistics']
            ]);
            $klaro_purposes[] = 'analytics';
        }


        SSConfig::modify()->merge(KlaroConfig::class, 'klaro_services', [
            'googleanalytics' => [
                'title' => 'Google Analytics',
                'description' => 'Analytics software by Google',
                'default' => $klaro_enabled_by_default,
                'purposes' => $klaro_purposes,
                'cookies' => [ "/^_ga(_.*)?/" ],
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
        KlaroRequirements::customKlaroScript($script, 'googleanalytics');
    }
}
