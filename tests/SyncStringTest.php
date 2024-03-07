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
    public function testToString() : void
    {
        $todayDt = new DateTime("2024-3-5T8:00:00Z",new \DateTimeZone('America/New_York'));
        $endDt = (new DateTime("2024-3-5T8:00:00Z",new \DateTimeZone('America/New_York')))
            ->add(date_interval_create_from_date_string('1 days'));

        $testStr = (new SampleSyncApp\Utils())->toSyncString($todayDt, $endDt);
        $this->assertEquals($testStr,"Synchronizing data between 2024-03-05 and 2024-03-06.");
    }
}
