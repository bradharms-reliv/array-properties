<?php

namespace Reliv\ArrayProperties;

use Reliv\ArrayProperties\Exception\ArrayPropertyException;
use Reliv\ArrayProperties\Exception\ArrayPropertyMissing;
use Reliv\ArrayProperties\Exception\IllegalArrayProperty;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class Property
{
    protected static $debug = false;
    protected static $depth = 2;

    /**
     * @param bool $debug
     * @param int  $depth
     *
     * @return void
     */
    public static function bootstrap(bool $debug, int $depth = 2)
    {
        self::$debug = $debug;
        self::$depth = $depth;
    }

    /**
     * @return bool
     */
    protected static function isDebug(): bool
    {
        return self::$debug;
    }

    /**
     * @param array  $params
     * @param string $key
     * @param        $value
     *
     * @return array
     */
    public static function set(
        array $params,
        string $key,
        $value
    ) {
        $params[$key] = $value;

        return $params;
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function get(
        array $params,
        string $key,
        $default = null
    ) {
        if (self::has($params, $key)) {
            return $params[$key];
        }

        return $default;
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return int|null
     */
    public static function getInt(
        array $params,
        string $key,
        $default = null
    ) {
        if (self::has($params, $key)) {
            return (int)$params[$key];
        }

        return $default;
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return bool|null
     */
    public static function getBool(
        array $params,
        string $key,
        $default = null
    ) {
        if (self::has($params, $key)) {
            return (bool)$params[$key];
        }

        return $default;
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return string|null
     */
    public static function getString(
        array $params,
        string $key,
        $default = null
    ) {
        if (self::has($params, $key)) {
            return (string)$params[$key];
        }

        return $default;
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return array|null
     */
    public static function getArray(
        array $params,
        string $key,
        $default = null
    ) {
        if (self::has($params, $key)) {
            return (array)$params[$key];
        }

        return $default;
    }

    /**
     * @param array              $params
     * @param string             $key
     * @param null|string|object $context
     *
     * @return mixed
     * @throws \Throwable|ArrayPropertyException
     */
    public static function getRequired(
        array $params,
        string $key,
        $context = null
    ) {
        self::assertHas($params, $key, $context);

        return $params[$key];
    }

    /**
     * @param array  $params
     * @param string $key
     *
     * @return bool
     */
    public static function has(
        array $params,
        string $key
    ) {
        return array_key_exists($key, $params);
    }

    /**
     * @param array  $params
     * @param string $key
     *
     * @return bool
     */
    public static function isEmpty(
        array $params,
        string $key
    ) {
        return empty(self::get($params, $key, null));
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function getDefaultIfEmpty(
        array $params,
        string $key,
        $default = null
    ) {
        if (self::isEmpty($params, $key)) {
            return $default;
        }

        return self::get(
            $params,
            $key,
            $default
        );
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function getAndRemove(
        array &$params,
        string $key,
        $default = null
    ) {
        $value = self::get(
            $params,
            $key,
            $default
        );

        self::remove(
            $params,
            $key
        );

        return $value;
    }

    /**
     * @param array              $params
     * @param string             $key
     * @param null|string|object $context
     *
     * @return mixed
     * @throws \Throwable|ArrayPropertyException
     */
    public static function getAndRemoveRequired(
        array &$params,
        string $key,
        $context = null
    ) {
        $value = self::getRequired(
            $params,
            $key,
            $context
        );

        self::remove(
            $params,
            $key
        );

        return $value;
    }

    /**
     * @param array  $params
     * @param string $key
     *
     * @return void
     */
    public static function remove(
        array &$params,
        string $key
    ) {
        unset($params[$key]);
    }

    /**
     * @param array              $params
     * @param string             $key
     * @param null|string|object $context
     *
     * @return void
     * @throws \Throwable|ArrayPropertyException
     */
    public static function assertNotEmpty(
        array $params,
        string $key,
        $context = null
    ) {
        self::assertHas(
            $params,
            $key,
            $context
        );

        $value = self::get(
            $params,
            $key,
            null
        );

        if (empty($value)) {
            $message = self::buildErrorMessage(
                $params,
                $key,
                "Property ({$key}) is missing and is required and can not be empty",
                $context
            );
            self::throwParamException(
                $params,
                $key,
                new ArrayPropertyMissing($message)
            );
        }
    }

    /**
     * @param array              $params
     * @param string             $key
     * @param null|string|object $context
     *
     * @return void
     * @throws \Throwable|ArrayPropertyException
     */
    public static function assertHas(
        array $params,
        string $key,
        $context = null
    ) {
        if (self::has($params, $key)) {
            return;
        }

        $message = self::buildErrorMessage(
            $params,
            $key,
            "Property ({$key}) is missing and is required",
            $context
        );

        self::throwParamException(
            $params,
            $key,
            new ArrayPropertyMissing($message)
        );
    }

    /**
     * @param array              $params
     * @param string             $key
     * @param null|string|object $context
     *
     * @return void
     * @throws \Throwable|ArrayPropertyException
     */
    public static function assertNotHas(
        array $params,
        string $key,
        $context = null
    ) {
        if (!self::has($params, $key)) {
            return;
        }

        $message = self::buildErrorMessage(
            $params,
            $key,
            "Illegal property ({$key}) is was found",
            $context
        );

        self::throwParamException(
            $params,
            $key,
            new IllegalArrayProperty($message)
        );
    }

    /**
     * @param array           $params
     * @param string          $key
     * @param \Throwable|null $exception
     *
     * @return void
     * @throws \Throwable|ArrayPropertyException
     */
    protected static function throwParamException(
        array $params,
        string $key,
        \Throwable $exception
    ) {
        $message = $exception->getMessage();

        if (self::isDebug()) {
            echo(
                "\n<pre>\n"
                . "Param Error: " . $message
                . "\n key: {$key}"
                // . "\n params: " . Json::encode($params, JSON_PRETTY_PRINT, 3)
                . "\n params dump: " . var_export($params, true)
                . "\n</pre>\n"
            );
        }

        throw $exception;
    }

    /**
     * @param array              $params
     * @param string             $key
     * @param null               $message
     * @param null|string|object $context
     *
     * @return null|string
     */
    protected static function buildErrorMessage(
        array $params,
        string $key,
        $message = null,
        $context = null
    ): string {
        if (empty($message)) {
            $message = 'There was an error with a key: (' . $key . ')'
                . ' in params: (' . json_encode($params, 0, 3) . ')';
        }

        $contextType = gettype($context);

        if (is_object($context)) {
            $context = get_class($context);
        }

        if ($context === null) {
            return $message;
        }

        // Clear json_last_error()
        json_encode(null);

        $json = json_encode($context, JSON_PRETTY_PRINT, self::$depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $message;
        }

        return $message . " - Context ({$contextType}): \n" . $json;
    }
}
