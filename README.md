# Silverstripe Google Suite

[![🎭 Tests](https://github.com/syntro-opensource/silverstripe-google-suite/workflows/%F0%9F%8E%AD%20Tests/badge.svg)](https://github.com/syntro-opensource/silverstripe-google-suite/actions?query=workflow%3A%22%F0%9F%8E%AD+Tests%22+branch%3A%22master%22)
[![codecov](https://codecov.io/gh/syntro-opensource/silverstripe-google-suite/branch/master/graph/badge.svg)](https://codecov.io/gh/syntro-opensource/silverstripe-google-suite)
![Dependabot](https://img.shields.io/badge/dependabot-active-brightgreen?logo=dependabot)
[![phpstan](https://img.shields.io/badge/PHPStan-enabled-success)](https://github.com/phpstan/phpstan)
[![composer](https://img.shields.io/packagist/dt/syntro/silverstripe-google-suite?color=success&logo=composer)](https://packagist.org/packages/syntro/silverstripe-google-suite)
[![Packagist Version](https://img.shields.io/packagist/v/syntro/silverstripe-google-suite?label=stable&logo=composer)](https://packagist.org/packages/syntro/silverstripe-google-suite)

Adds Google Analytics and Ads conversion tracking to your Website. Uses [`syntro/silverstripe-klaro`](https://github.com/syntro-opensource/silverstripe-klaro)
for consent management.

## Installation
To install this module, run the following command:
```
composer require syntro/silverstripe-google-suite
```

## Usage
Usage of this module is pretty straightforward. As soon as one of the
products is enabled, the dependencies are injected into to every page.
Consent management is taken care of by klaro!, which only loads the tracking
scripts after the user accepts.

There are two configs which handle injecting the respective dependencies:

* [Syntro\SilverstripeGoogleSuite\AnalyticsConfig](src/AnalyticsConfig.php)
* [Syntro\SilverstripeGoogleSuite\AdsConfig](src/AdsConfig.php)

Each of the mentioned configs is enabled by adding tokens which you get when
registring for Ads or Analytics. You can add multiple tokens to send
events to multiple accounts. Additional options for the `gtag('cofig', ...)` tag
can be specified too:

```yml
Syntro\SilverstripeGoogleSuite\AnalyticsConfig:
  google_tokens:
    - X-XXXXXXXXX
    - token: X-XXXXXXXXX
      title: Some Ads Account # set a title for backend use
      option: value
```

After adding at least one tag, the respective product becomes active.

### Google Analytics
This module is intended to be used with the new properties. After configuring the
tokens, everything should be working out of the box.

### Google Ads Conversion Tracking

In order to track conversions on your page, you have to configure said conversions
in your Ads account. A conversion has a label assigned to it which you need in order
to send it to the correct container.

To inject automated conversions in your page, you have to define them in the config:

```yml
Syntro\SilverstripeGoogleSuite\AdsConfig:
  google_tokens:
    - token: XX-XXXXXXXXXXXX
  onclick_conversion:
    - selector: "a[href*=tel]"
      conversion_label: XxXXxxXXXXxx
    - selector: a[href^="mailto:info@domain.com"]
      conversion_label: XxXXxxXXXXxx
    - selector: a[href*="shop.domain.com"]
      conversion_label: XxXXxxXXXXxx
  onsubmit_conversion:
    - selector: "form[id*=SomeForm]"
      conversion_label: XxXXxxXXXXxx
      conversion_id: XX-XXXXXXXXXXXX
```
There are currently two kinds of automated conversions:

* `onclick_conversion`: these are triggered when the user clicks on an element.
* `onsubmit_conversion`: these are triggered when the user submits a form.

Every automated conversion can have the following keys:
* `selector`: a CSS selector which identifies the DOM-Nodes you want to track
* `conversion_label`: the label of the conversion
* `conversion_id`: (optional) the conversion id to use when emitting the event. By default, the first configured token is used, but in the edge-case where you need to add multiple tokens, use this option to define the target.
* `conversion_url`: (optional) should not be used. This overwrites the normal behaviour by forcing the user to this url.

> ## Styling klaro!
> This Module uses klaro! for consent management via the [`syntro/silverstripe-klaro`](https://github.com/syntro-opensource/silverstripe-klaro)
> module. We recommend checking that module out for information on how to style the
> consent-window.
