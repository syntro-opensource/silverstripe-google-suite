<?php
namespace Syntro\SilverstripeGoogleSuite\Extension;

use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\TabSet;
use SilverStripe\Core\Extension;
use SilverStripe\Control\Controller;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Syntro\SilverstripeGoogleSuite\AdsConfig;
use Syntro\SilverstripeGoogleSuite\Model\Conversion;

/**
 * Adds an SEM tab to the page in question which allows the user to add or remove
 * onload conversion to a specific page
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 * @codeCoverageIgnore
 */
class AdsSitetreeExtension extends Extension
{
    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'GoogleConversions' => Conversion::class,
    ];

    /**
     * Update Fields
     * @param  FieldList $fields the fields forom Parent
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;

        if (!AdsConfig::isEnabled()) {
            return $fields;
        }



        $fields->findOrMakeTab(
            "Root.Ads",
            $owner->fieldLabel('Root.Ads')
        );

        $fields->addFieldToTab(
            'Root.SEO',
            TabSet::create('AdsRoot')
        );

        $fields->removeByName([
            'GoogleConversions',
            'Root.GoogleConversions',
        ]);

        $gridfield = GridField::create(
            'GoogleConversions',
            'Google Ads Conversions',
            $owner->GoogleConversions(),
            GridFieldConfig::create()
                ->addComponent(new GridFieldButtonRow('before'))
                // ->addComponent(new GridFieldToolbarHeader())
                ->addComponent(new GridFieldTitleHeader())
                ->addComponent($editableColumns = new GridFieldEditableColumns())
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldAddNewInlineButton())
        );

        $editableColumns->setDisplayFields([
            'GoogleToken' => [
                'title' => _t(Conversion::class . '.GoogleToken', 'Google Ads Account'),
                'callback' => function () {
                    $tokens = AdsConfig::getGoogleTokens();
                    $sanitizedTokens = [];
                    foreach ($tokens as $value) {
                        if (is_array($value)) {
                            $sanitizedTokens[$value['token']] = isset($value['title']) ? $value['title'] : $value['token'];
                        } else {
                            $sanitizedTokens[$value] = $value;
                        }
                    }
                    return DropdownField::create(
                        'GoogleToken',
                        _t(Conversion::class . '.GoogleToken', 'Google Ads Account'),
                        $sanitizedTokens
                    );
                },
            ],
            'ConversionLabel' => [
                'title' => _t(Conversion::class . '.ConversionLabel', 'Conversion Label'),
                'callback' => function () {
                    return TextField::create(
                        'ConversionLabel',
                        _t(Conversion::class . '.ConversionLabel', 'Conversion Label')
                    );
                },
            ],
        ]);
        $helptext = _t(__CLASS__ . '.GoogleAdsDescription', 'The conversions entered below are triggered when this page is loaded.');
        $fields->addFieldsToTab(
            'Root.Ads',
            [
                HeaderField::create(
                    'GoogleAdsHeader',
                    _t(__CLASS__ . '.GoogleAdsHeader', 'Google Ads Pageload Conversions')
                ),
                LiteralField::create(
                    'GoogleAdsDescription',
                    <<<HTML
                        <hr/>
                            $helptext
                        <br><br>
                    HTML
                ),
                $gridfield
            ]
        );

        return $fields;
    }

    /**
     * updateFieldLabels - adds Fieldlabels
     *
     * @param  array $labels the original labels
     * @return array
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['Root.Ads'] =  _t(__CLASS__ . '.ADS', 'Ads');
        return $labels;
    }
}
