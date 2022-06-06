<?php

namespace Syntro\SilverstripeGoogleSuite\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;
use Syntro\SilverstripeKlaro\KlaroRequirements;

/**
 * Represents a conversion action
 *
 * @author Matthias Leutenegger
 */
class Conversion extends DataObject
{

    /**
     * Defines the database table name
     * @config
     * @var string
     */
    private static $table_name = 'GoogleAdsConversion';

    /**
     * Database fields
     * @config
     * @var array
     */
    private static $db = [
        'GoogleToken' => 'Varchar',
        'ConversionLabel' => 'Varchar',
    ];

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @config
     * @var array
     */
    private static $summary_fields = [
        'GoogleToken' => 'GoogleToken',
        'ConversionLabel' => 'ConversionLabel'
    ];

    /**
     * Has_one relationship
     * @config
     * @var array
     */
    private static $has_one = [
        'Page' => SiteTree::class,
    ];

    /**
     * updateFieldLabels - adds Fieldlabels
     *
     * @param  bool $includerelations include relations
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels(true);
        $labels['GoogleToken'] =  _t(__CLASS__ . '.GoogleToken', 'Google Ads Account');
        $labels['ConversionLabel'] =  _t(__CLASS__ . '.ConversionLabel', 'Conversion Label');
        return $labels;
    }

    /**
     * requireSnippet - adds the conversion Snippet to the Page
     *
     * @return void
     */
    public function requireSnippet()
    {
        $token = $this->GoogleToken;
        $label = $this->ConversionLabel;
        KlaroRequirements::customKlaroScript(
            <<<JS
                gtag('event', 'conversion', {'send_to': '$token/$label'});
            JS
            ,
            'googleadstracking'
        );
    }
}
