<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitce9622e7eff34f64ac73e765bcedef80
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Matijajanc\\Postman\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Matijajanc\\Postman\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitce9622e7eff34f64ac73e765bcedef80::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitce9622e7eff34f64ac73e765bcedef80::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitce9622e7eff34f64ac73e765bcedef80::$classMap;

        }, null, ClassLoader::class);
    }
}
