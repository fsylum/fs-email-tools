<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit68ec25abe72e948bd5712172a76f5c44
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Fsylum\\EmailTools\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Fsylum\\EmailTools\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit68ec25abe72e948bd5712172a76f5c44::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit68ec25abe72e948bd5712172a76f5c44::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit68ec25abe72e948bd5712172a76f5c44::$classMap;

        }, null, ClassLoader::class);
    }
}
