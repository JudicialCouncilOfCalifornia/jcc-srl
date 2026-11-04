<?php

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

/**
 * Load any .env file. See /.env.example.
 *
 * createUnsafeImmutable() keeps the v2 behavior: it never overwrites existing
 * variables and fills getenv(), $_ENV and $_SERVER. safeLoad() does nothing
 * when there is no .env file (production).
 */
Dotenv\Dotenv::createUnsafeImmutable(__DIR__)->safeLoad();
