<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9ace61b2e83370e9b65a766b38eeb952
{
    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpspider\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpspider\\' => 
        array (
            0 => __DIR__ . '/..' . '/owner888/phpspider',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9ace61b2e83370e9b65a766b38eeb952::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9ace61b2e83370e9b65a766b38eeb952::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
