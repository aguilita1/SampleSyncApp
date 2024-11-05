<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Aguilita
 * Date: 3/5/2024
 * Time: 7:27 PM
 */
if (!defined('PROJECT_ROOT'))
    define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

//Set Docker Default Variables
$_SERVER['SA_SYNC_INTERVAL'] = 30;
$_SERVER['SA_START_SYNC'] = '04:00:00';
$_SERVER['SA_STOP_SYNC'] = '23:59:59';
$_SERVER['SA_TIME_ZONE'] = 'America/New_York';

require PROJECT_ROOT . '/src/Utils.php';
use PHPUnit\Framework\TestCase;

//Set Default Timezone
date_default_timezone_set($_SERVER['SA_TIME_ZONE']);

final class SyncStringTest extends TestCase
{
    // Test for the `toSyncString` method.
    public function testToString() : void
    {
        // Create a DateTime object for the start date, "2024-03-05 08:00:00 UTC",
        // in the 'America/New_York' timezone
        $todayDt = new DateTime("2024-3-5T8:00:00Z",new \DateTimeZone('America/New_York'));
        // Create a DateTime object for the end date, one day after the start date
        $endDt = (new DateTime("2024-3-5T8:00:00Z",new \DateTimeZone('America/New_York')))
            ->add(date_interval_create_from_date_string('1 days'));

        // Call the `toSyncString()` method from the `SampleSyncApp\Utils` class
        $testStr = (new SampleSyncApp\Utils())->toSyncString($todayDt, $endDt);
        // Assert that the output string matches the expected string
        $this->assertEquals($testStr,"Synchronizing data between 2024-03-05 and 2024-03-06.");
    }
}
