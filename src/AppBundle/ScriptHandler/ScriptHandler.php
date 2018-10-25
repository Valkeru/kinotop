<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 25.10.18
 * Time: 12:44
 */

namespace AppBundle\ScriptHandler;

use Composer\Script\Event;

class ScriptHandler extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    public static function updateDatabaseSchema(Event $event): void
    {
        $consoleDir = static::getConsoleDir($event, 'update database schema');
        static::executeCommand($event, $consoleDir, 'doctrine:schema:update -f');
    }
}
