<?php
$EM_CONF[$_EXTKEY] = array(
    'title'                         => 'Protect Frontend Login',
    'description'                   => 'this typo3 extension provides brute force protection for fel_login with device cookies as described in OWASP https://www.owasp.org/index.php/Slow_Down_Online_Guessing_Attacks_with_Device_Cookies',
    'category'                      => 'services',
    'author'                        => 'Johannes Seipelt',
    'author_email'                  => 'johannes.seipelt@3m5.de',
    'author_company'                => '3m5.',
    'state'                         => 'beta',
    'uploadfolder'                  => 0,
    'createDirs'                    => '',
    'modify_tables'                 => '',
    'clearCacheOnLoad'              => 0,
    'version'                       => '1.0.0',
    'constraints'                   =>
        array(
            'depends'   =>
                array(
                    'php'   => '7.2.0-7.4.0',
                    'typo3' => '9.5.0-10.4.99',
                ),
            'conflicts' =>
                array(
                    'felogin_bruteforce_protection' => '*',
                    'secure_login' => '*',
                    'loginlimit' => '*'
                ),
            'suggests'  =>
                array(),
        ),
);
