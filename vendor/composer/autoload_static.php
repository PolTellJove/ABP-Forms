<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;


class ComposerStaticInit44da96072fb7420109460fa20ff57652
class ComposerStaticInitba79b909a94825f9ae6468f70b41c40f
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit44da96072fb7420109460fa20ff57652::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit44da96072fb7420109460fa20ff57652::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit44da96072fb7420109460fa20ff57652::$classMap;
            $loader->prefixLengthsPsr4 = ComposerStaticInitba79b909a94825f9ae6468f70b41c40f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitba79b909a94825f9ae6468f70b41c40f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitba79b909a94825f9ae6468f70b41c40f::$classMap;

        }, null, ClassLoader::class);
    }
}
