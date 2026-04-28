<?php

use Cloudinary\Configuration\Configuration;


// Global config: Affects all usage of Cloudinary SDK globally
Configuration::instance([
  'cloud' => [
    'cloud_name' => 'dxfqsi56h',
    'api_key'    => '524847167326336',
    'api_secret' => 'JHQXMiU9y-T-nBhOdaMC4mPv9ZQ'
  ],
  'url' => [
    'secure' => true
  ]
]);


// Or configure programmatically
$config = Configuration::instance();
$config->cloud->cloudName = 'dxfqsi56h';
$config->cloud->apiKey = '524847167326336';
$config->cloud->apiSecret = 'JHQXMiU9y-T-nBhOdaMC4mPv9ZQ';
$config->url->secure = true;
?>