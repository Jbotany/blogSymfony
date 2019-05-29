<?php


namespace App\Service;



class Slugify
{

    private $replaceLetters = ['à' => 'a', 'é' => 'e', 'è' => 'e', 'ç' => 'c'];

    public function generate(string $input) : string
    {
        $result = preg_replace('/[!@#$%^&*()_+\={};\':"\\|,.<>\/?]/','', $input);

        foreach ($this->replaceLetters as $forbiddenLetter => $replaceLetter) {
            if (array_key_exists($forbiddenLetter, $this->replaceLetters)) {
                $result = str_replace($forbiddenLetter, $replaceLetter, $result);
            }
        }

        return str_replace(' ', '-', trim($result));
    }
}