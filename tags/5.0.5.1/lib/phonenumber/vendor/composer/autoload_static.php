<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf96c5d0121d2cc562741c146f50fadd
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'l' => 
        array (
            'libphonenumber\\' => 15,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
        ),
        'G' => 
        array (
            'Giggsey\\Locale\\' => 15,
        ),
        'B' => 
        array (
            'Brick\\PhoneNumber\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'libphonenumber\\' => 
        array (
            0 => __DIR__ . '/..' . '/giggsey/libphonenumber-for-php/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Giggsey\\Locale\\' => 
        array (
            0 => __DIR__ . '/..' . '/giggsey/locale/src',
        ),
        'Brick\\PhoneNumber\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
            1 => __DIR__ . '/..' . '/brick/phonenumber/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf96c5d0121d2cc562741c146f50fadd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf96c5d0121d2cc562741c146f50fadd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcf96c5d0121d2cc562741c146f50fadd::$classMap;

        }, null, ClassLoader::class);
    }
}