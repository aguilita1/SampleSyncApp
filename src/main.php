#!/usr/bin/env php
<?php
/*
 * Sample Sync Application written in PHP to test out Github Actions.
 * Copyright (C) 2024 Daniel Kelley
 * (main.php)
 */
    define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

    require PROJECT_ROOT . '/vendor/autoload.php';
    require PROJECT_ROOT . '/lib/Utils.php';

    global $app_version;
	$app_version = 'APP_VERSION';

    global $syncInterval;
	global $syncStart;
    global $syncEnd;

    //Define synchronization environment variables
    $syncInterval= $_SERVER['SA_SYNC_INTERVAL'];
    $syncStart= $_SERVER['SA_START_SYNC'];
    $syncEnd= $_SERVER['SA_STOP_SYNC'];
    //Set Default Timezone
    date_default_timezone_set($_SERVER['SA_TIME_ZONE']);

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    global $increment;
    $increment = 0;

    try {
        // [0] initialize
        // Set up logging to file
        $log = new \Monolog\Logger('SampleSyncApp',[],[], null);
        /** @phpstan-ignore-next-line */
        $stream = new \Monolog\Handler\StreamHandler( fopen('php://stdout', 'w'), \Monolog\Level::Debug);
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
     * @param \Monolog\Logger $log
      */
    function Sync(\Monolog\Logger $log ) : void
    {
         $todayDt = new DateTime();
        $endDt = (new DateTime())->add(DateInterval::createFromDateString('1 days'));
        $log->info('*******************************************************');
        $log->info((new SampleSyncApp\Utils())->toSyncString($todayDt, $endDt));
        $log->info('Please wait... doing meaningless pretend work again.');
        $log->info('Synchronization is complete.');
        $log->info('*******************************************************');
    }


?>