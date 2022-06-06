<?php
namespace Syntro\SilverstripeGoogleSuite\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\Control\Director;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Core\Config\Config as SSConfig;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\FieldType\DBHTMLText;
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
            if (Director::isLive() && Versioned::get_stage() == Versioned::LIVE) {
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

    /**
     * afterCallActionHandler - adds the snippets for the pageload conversions
     *
     * @param  HTTPRequest             $request the current request
     * @param  string                  $action  the current action
     * @param  DBHTMLText|HTTPResponse $result  the current result
     * @return void
     */
    public function beforeCallActionHandler($request, $action, $result)
    {
        $owner = $this->getOwner()->data();
        $data = $owner->data();
        if ($data instanceof \SilverStripe\CMS\Model\SiteTree &&
            Director::isLive() &&
            Versioned::get_stage() == Versioned::LIVE &&
            // $owner->getAction() === 'index' &&
            $data->GoogleConversions()->count() > 0
        ) {
            foreach ($data->GoogleConversions() as $conversion) {
                $conversion->requireSnippet();
            }
        }
    }
}
