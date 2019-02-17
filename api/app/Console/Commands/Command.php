<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/16/19
 * Time: 5:43 PM
 */

namespace App\Console\Commands;

use Illuminate\Console\Command as LaravelCommand;

abstract class Command extends LaravelCommand
{
    protected function askWithValidation(
        string $question,
        callable $validation,
        string $errorMessage,
        bool $isSecret = false,
        bool $firstTime = true
    ): string
    {
        if (!$firstTime) {
            $this->error($errorMessage);
        }

        $answer = $isSecret ? $this->secret($question) : $this->ask($question);

        if (!$validation($answer)) {
            return $this->askWithValidation(
                $question,
                $validation,
                $errorMessage,
                $isSecret,
                false
            );
        }

        return $answer;
    }
}