#!/usr/bin/env php
<?php
/*
 * Sample Sync Application written in PHP to test out GitHub Actions.
 * Copyright (C) 2024 Daniel Kelley
 * (main.php)
 */

declare(strict_types=1);

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require PROJECT_ROOT . '/vendor/autoload.php';
require PROJECT_ROOT . '/lib/Utils.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Level;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use SampleSyncApp\Utils;

// Load configuration from environment variables
$syncInterval = (int)($_SERVER['SA_SYNC_INTERVAL'] ?? 60);
$syncStart = $_SERVER['SA_START_SYNC'] ?? '00:00:00';
$syncEnd = $_SERVER['SA_STOP_SYNC'] ?? '23:59:59';
date_default_timezone_set($_SERVER['SA_TIME_ZONE'] ?? 'UTC');

// Initialize Logger
$log = initializeLogger();

try {
    $log->info(sprintf('Starting up - SampleSyncApp version: %s PROJECT_ROOT=%s', 'APP_VERSION', PROJECT_ROOT));
    $log->debug('*****************START**main.php*********************');

    // Create the event loop instance
    /** @var LoopInterface $loop */
    $loop = new StreamSelectLoop();

    $loop->addPeriodicTimer($syncInterval, function () use ($log, $syncInterval, $syncStart, $syncEnd) {
        $dateToCompare = date('H:i:s');

        try {
            if ($dateToCompare >= $syncStart && $dateToCompare <= $syncEnd) {
                $log->info(sprintf(
                    "Tick - %d more seconds. Inside sync interval, start = %s and end = %s.",
                    $syncInterval,
                    $syncStart,
                    $syncEnd
                ), [
                    'memoryAllocated' => memory_get_usage(),
                    'peakMemoryAllocated' => memory_get_peak_usage()
                ]);
                performSync($log);
                gc_collect_cycles();
                gc_mem_caches();
            } else {
                $log->info(sprintf("Tick - skipped. Outside sync interval, start = %s and end = %s.", $syncStart, $syncEnd));
            }
        } catch (TypeError $e) {
            $log->error('TypeError Exception: ' . $e->getMessage());
        }
    });

    $loop->run();
} catch (Exception $ex) {
    $log->error('Unknown Exception: ' . $ex->getMessage());
}

/**
 * Initialize the logger.
 *
 * @return Logger
 */
function initializeLogger(): Logger
{
    $logger = new Logger('SampleSyncApp');
    $streamHandler = new StreamHandler('php://stdout', Monolog\Level::Debug);
    $streamHandler->setFormatter(new JsonFormatter());
    $logger->pushHandler($streamHandler);

    return $logger;
}

/**
 * Perform synchronization tasks.
 * @param Logger $log
 */
function performSync(Logger $log): void
{
    try {
        $todayDt = new DateTime();
        $endDt = (new DateTime())->add(new DateInterval('P1D'));

        $log->info('*******************************************************');
        $log->info((new Utils())->toSyncString($todayDt, $endDt));
        $log->info('Please wait... doing meaningless pretend work.');
        $log->info('Synchronization is complete.');
        $log->info('*******************************************************');
    } catch (Exception $ex) {
        $log->error("Exception: the interval_spec cannot be parsed as an interval.");
    }
}
?>