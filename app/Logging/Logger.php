<?php
namespace App\Logging;

use Illuminate\Foundation\Application;
use Illuminate\Log\Logger as BaseLogger;
use Illuminate\Log\LogManager;

class Logger extends LogManager {

    protected function tap($name, BaseLogger $logger){
        foreach($logger->getHandlers() as $handler){
            if($name === 'slack'){
                $handler->pushProcessor(function ($record) {
                    $record['extra']['url'] = '<' . request()->getUri() . '|' . request()->getHost() . '>';

                    if($ref = request()->header('referer')){
                        $refHost = @parse_url($ref, PHP_URL_HOST) ?: 'RefHost';
                        $record['extra']['referer'] = "<$ref|$refHost>";
                    }

                    return $record;
                });
            }
        }
        return parent::tap($name, $logger);
    }

    public static function register(Application $app){
        $app->extend(LogManager::class, function(LogManager $manager, Application $app){
            return new Logger($app);
        });
    }
}