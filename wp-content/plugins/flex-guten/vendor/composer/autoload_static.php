<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit245d89305495571a2798d8f7917ec6df
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Dwp\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Dwp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit245d89305495571a2798d8f7917ec6df::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit245d89305495571a2798d8f7917ec6df::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit245d89305495571a2798d8f7917ec6df::$classMap;

        }, null, ClassLoader::class);
    }
}
