<?php

$vendorDir = dirname(__DIR__);
$rootDir = dirname(dirname(__DIR__));

return array (
  'craftcms/guest-entries' => 
  array (
    'class' => 'craft\\guestentries\\Plugin',
    'basePath' => $vendorDir . '/craftcms/guest-entries/src',
    'handle' => 'guest-entries',
    'aliases' => 
    array (
      '@craft/guestentries' => $vendorDir . '/craftcms/guest-entries/src',
    ),
    'name' => 'Guest Entries',
    'version' => '4.0.1',
    'description' => 'This plugin allows you to save guest entries from the front-end of your website.',
    'developer' => 'Pixel & Tonic',
    'developerUrl' => 'https://pixelandtonic.com/',
    'developerEmail' => 'support@craftcms.com',
    'documentationUrl' => 'https://github.com/craftcms/guest-entries/blob/v2/README.md',
  ),
);
