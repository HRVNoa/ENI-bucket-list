<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Censurator
{


    public function __construct(private readonly ContainerBagInterface $bag)
    {
    }

    public function purify(string $text): string
    {
        $path = $this->bag->get('app.censurator_file');
        $badWords = explode("\n", file_get_contents($path)) ?? ['merde'];
        foreach ($badWords as $badWord) {
            $badWord = str_replace(PHP_EOL, '', $badWord);
            $text = str_ireplace($badWord, str_repeat('*', mb_strlen($badWord)), $text);
        }
        return $text;
    }
}