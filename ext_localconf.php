<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    $_EXTKEY,
    'auth',
    \Neamil\ProtectFeLogin\Service\AuthenticationService::class,
    array(
        'title' => 'secure frontend login with device cookies (brut force protection)',
        'description' => 'felogin protection with device cookies',
        'subtype' => 'authUserFE',
        'available' => true,
        'priority' => 90,
        'quality' => 90,
        'os' => '',
        'exec' => '',
        'className' => \Neamil\ProtectFeLogin\Service\AuthenticationService::class
    )
);
