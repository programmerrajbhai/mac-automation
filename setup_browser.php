<?php
require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

echo "ব্রাউজার ওপেন করা হচ্ছে...\n";

$options = new ChromeOptions();
$options->addArguments(['--disable-blink-features=AutomationControlled']);
$options->addArguments(['--user-data-dir=' . $profilePath]); 
$options->addArguments(['--disable-notifications']); 
$options->setExperimentalOption('excludeSwitches', ['enable-automation']);
$options->setExperimentalOption('useAutomationExtension', false);

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

// ব্রাউজার স্টার্ট করা হলো
$driver = RemoteWebDriver::create($serverUrl, $capabilities);