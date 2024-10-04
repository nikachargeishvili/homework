<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PlayHangman extends Command
{
    protected $signature = 'play:hangman';
    protected $description = 'Play a simple game of Hangman';

    public function handle()
    {
        do{
            $hiddenWord = $this->secret("შეიყვანეთ საიდუმლო სიტყვა:");
            $guessedLetters = [];
            $remainingAttempts = 6;
            while ($remainingAttempts > 0 && !$this->isWordGuessed($hiddenWord,$guessedLetters)) {
                $this->displayWord($hiddenWord,$guessedLetters);
                $guess = strtolower($this->ask("მეორე მოთამაშემ შეიყვანოს ასობგერა:"));
                if (!in_array($guess , str_split($hiddenWord))) {
                    $remainingAttempts--;
                    $this->info("არასწორად შეიყვანეთ ასო! დარჩენილი მცდელობებია :  $remainingAttempts");
                }
                $guessedLetters[] = $guess;
            }
            $this->displayWord($hiddenWord,$guessedLetters);
            $result = $this->isWordGuessed($hiddenWord,$guessedLetters) ? 'მოიგე':'წააგე';
            $this->info("შენ $result თამაში!");
            $this->saveGameResult($hiddenWord,$guessedLetters,$result);
        }while($this->confirm('კიდევ გსურთ თამაში?(yes/no)'));
    }

    private function displayWord($hiddenWord, $guessedLetters)
    {
        $display = '';

        foreach (str_split($hiddenWord) as $letter) {
            $display .= in_array($letter, $guessedLetters) ? $letter : '_';
            $display .= ' ';
        }

        $this->info(trim($display));
    }

    private function isWordGuessed($hiddenWord, $guessedLetters)
    {
        foreach (str_split($hiddenWord) as $letter) {
            if (!in_array($letter, $guessedLetters)) {
                return false;
            }
        }

        return true;
    }

    private function saveGameResult($hiddenWord, $guessedLetters, $result)
    {
        $logMessage = date('Y-m-d H:i:s') . " - თამაში $result. სიტყვა იყო: $hiddenWord. გამოცნობილი ასოებია: " . implode(', ', $guessedLetters) . "\n";
        file_put_contents(storage_path('logs/hangman.log'), $logMessage, FILE_APPEND);
    }
}
