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

* [AnalyticsConfig](src/AnalyticsConfig.php)
* [AdsConfig](src/AdsConfig.php)


> ## Styling klaro!
> This Module uses klaro! for consent management via the [`syntro/silverstripe-klaro`](https://github.com/syntro-opensource/silverstripe-klaro)
> module. We recommend checking that module out for information on how to style the
> consent-window.
