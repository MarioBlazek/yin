<?php

return (new Marek\CodingStandard\PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor', '.github', 'build', '.phpunit.cache'])
    )
    ;
