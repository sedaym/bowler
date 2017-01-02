<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit560cc2e5bfc86ab670b85e164a333f6b
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Vinelab\\Bowler\\' => 15,
        ),
        'P' => 
        array (
            'PhpAmqpLib\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Vinelab\\Bowler\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'PhpAmqpLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-amqplib/php-amqplib/PhpAmqpLib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit560cc2e5bfc86ab670b85e164a333f6b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit560cc2e5bfc86ab670b85e164a333f6b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}