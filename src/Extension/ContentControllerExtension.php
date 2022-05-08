<?php
namespace Syntro\SilverstripeGoogleSuite\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\Control\Director;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Core\Config\Config as SSConfig;
use Syntro\SilverstripeGoogleSuite\GTagConfig;
use Syntro\SilverstripeGoogleSuite\AnalyticsConfig;
use Syntro\SilverstripeGoogleSuite\AdsConfig;

/**
 * Extends the default content controller to include the analytics dependencies
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 * @codeCoverageIgnore
 */
class ContentControllerExtension extends Extension
{

    /**
     * onBeforeInit - Handler executed before init
     *
     * @return void
     */
    public function onBeforeInit()
    {
        if (AnalyticsConfig::isEnabled() || AdsConfig::isEnabled()) {
            GTagConfig::includeKlaroGlobalSiteTag();
            if (AnalyticsConfig::isEnabled()) {
                AnalyticsConfig::includeKlaroRequirements();
            }
            if (AdsConfig::isEnabled()) {
                AdsConfig::includeKlaroRequirements();
            }
            if (true || Director::isLive() && Versioned::get_stage() == Versioned::LIVE) {
                GTagConfig::includeGlobalSiteTag();
                if (AnalyticsConfig::isEnabled()) {
                    AnalyticsConfig::includeFrontendRequirements();
                }
                if (AdsConfig::isEnabled()) {
                    AdsConfig::includeFrontendRequirements();
                }
            }
        }
    }
}
