#!/usr/bin/env php
<?php
/*
 * Sample Sync Application written in PHP to test out Github Actions.
 * Copyright (C) 2024 Daniel Kelley
 * (main.php)
 */
define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require PROJECT_ROOT . '/vendor/autoload.php';

    global $app_version;
	$app_version = 'APP_VERSION';

    global $syncStart;
    global $syncEnd;
        global $delayedRetryTime;
    //Define synchronization environment variables
    $syncInterval= $_SERVER['SA_SYNC_INTERVAL'];
    $syncStart= $_SERVER['SA_START_SYNC'];
    $syncEnd= $_SERVER['SA_STOP_SYNC'];

    //session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    global $increment;
    $increment = 0;
try {
// [0] initialize
    // Set up logging to file
        $log = new \Monolog\Logger('SampleSyncApp');
    $stream = new \Monolog\Handler\StreamHandler(fopen('php://stdout', 'w'), \Monolog\Level::Debug);
    $stream->setFormatter(new \Monolog\Formatter\JsonFormatter());
    $log->pushHandler($stream);
    $log->info(sprintf('Starting up - SampleSyncApp version: %s PROJECT_ROOT=%s', $GLOBALS['app_version'], PROJECT_ROOT));

    $log->debug('*****************START**main.php*********************');


// [1]
    $loop = React\EventLoop\Factory::create();

// [2]
    $loop->addPeriodicTimer($syncInterval, function () use ($log, $syncInterval, $syncStart, $syncEnd) {

        $dateToCompare = date('H:i:s');
        try {
            if ($dateToCompare >= $syncStart && $dateToCompare <= $syncEnd) {
                $log->info(sprintf("Tick - %d more seconds. Inside sync interval, start = %s and end = %s.", $syncInterval, $syncStart, $syncEnd),
                    array('memoryAllocated' => memory_get_usage(), 'peakMemoryAllocated' => memory_get_peak_usage()));
                Sync($log);
                gc_collect_cycles();
                gc_mem_caches();
            } else {
                $log->info(sprintf("Tick - skipped. Outside sync interval, start = %s and end = %s.", $syncStart, $syncEnd));
            }
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            $log->error('Connect Exception: ' . $e->getMessage() . $e->getTraceAsString());
            error_log('Connect Exception:' . $e->getMessage() . $e->getTraceAsString() . PHP_EOL);
        } catch (GuzzleHttp\Exception\RequestException $e3) {
            $log->error('Request Exception: ' . $e3->getMessage() . $e3->getTraceAsString());
            error_log('Request Exception:' . $e3->getMessage() . $e3->getTraceAsString() . PHP_EOL);
        } catch (TypeError $e4) {
            $log->error('TypeError Exception: ' . $e4->getMessage() . $e4->getTraceAsString());
            error_log('TypeError Exception:' . $e4->getMessage() . $e4->getTraceAsString() . PHP_EOL);
        }

    });

// [3]
    $loop->run();
}catch(Exception $ex){
    error_log('Unknown Exception:' . $ex->getMessage() . $ex->getTraceAsString() . PHP_EOL);
}

/**
 * @param \Monolog\Logger
  */
function Sync(\Monolog\Logger $log )
{
     $todayDt = new DateTime();
    $endDt = (new DateTime())->add(date_interval_create_from_date_string('1 days'));
    $log->info('*******************************************************');
    $log->info('Synchronizing data between ' . $todayDt->format("Y-m-d") . ' and '. $endDt->format("Y-m-d").'.');
    $log->info('Please wait... doing meaningless pretend work.');
    $log->info('Synchronization is complete.');
    $log->info('*******************************************************');
}

?>