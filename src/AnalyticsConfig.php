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
class AnalyticsConfig extends Config
{

    /**
     * the name of the default purpose
     * @config
     * @var string
     */
    private static $klaro_default_purpose = 'analytics';

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
        $klaro_default_purpose = SSConfig::inst()->get(static::class, 'klaro_default_purpose');
        $klaro_required = static::isKlaroRequired();

        if ($klaro_create_default_purpose) {
            SSConfig::modify()->merge(KlaroConfig::class, 'klaro_purposes', [
                $klaro_default_purpose => ['title' => 'Analytics', 'description' => 'Tools used to gather usage statistics']
            ]);
            $klaro_purposes[] = $klaro_default_purpose;
        }


        SSConfig::modify()->merge(KlaroConfig::class, 'klaro_services', [
            'googleanalytics' => [
                'title' => 'Google Analytics',
                'description' => 'Analytics software by Google',
                'default' => $klaro_enabled_by_default,
                'required' => $klaro_required,
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
