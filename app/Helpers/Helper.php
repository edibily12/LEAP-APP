<?php

namespace App\Helpers;

class Helper
{
    public static function includeRoutes($folder): void
    {
        //loop recursively to the web folder;
        $directory = new \RecursiveDirectoryIterator($folder);

        /** @var \RecursiveDirectoryIterator | \RecursiveIteratorIterator $iterator */
        $iterator = new \RecursiveIteratorIterator($directory);

        //require files inside web directory
        while ($iterator->valid()){
            //check if the file exist then require that file
            if (!$iterator->isDot() && $iterator->isFile() && $iterator->isReadable() && $iterator->current()->getExtension() === 'php'){
                require $iterator->key();
            }

            $iterator->next();
        }
    }

    public static function calculateDelaySeconds($dateToString, $time): int
    {
        $dateTimeString = $dateToString . ' ' . $time;

        $dateTime = \Carbon\Carbon::parse($dateTimeString);

        $currentTime = \Carbon\Carbon::now();

        $minutesDifference = $currentTime->diffInMinutes($dateTime);

        return round($minutesDifference) * 60;
    }


    public static function increaseStep($currentStep, $totalStep)
    {
        $currentStep++;
        if ($currentStep > $totalStep) {
            $currentStep = $totalStep;
        }
        return $currentStep;
    }

    public static function decreaseStep($currentStep): int
    {
        $currentStep--;
        if ($currentStep < 1) {
            $currentStep = 1;
        }

        return $currentStep;
    }
}