<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit988e5d20667487a77d050bc8619bfb30
{
    public static $prefixLengthsPsr4 = array (
        'o' => 
        array (
            'oldtailor\\oauth\\' => 16,
        ),
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'oldtailor\\oauth\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit988e5d20667487a77d050bc8619bfb30::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit988e5d20667487a77d050bc8619bfb30::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
