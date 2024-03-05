<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class PregReplaceCallbackTest extends TestCase
{
    public function testPregReplaceIncremented(): void
    {
        $string = 'Article ${index}, Article ${index}, Article ${index}';
        $i = 0;
        $replacement = preg_replace_callback('/Article \$\{index\}/', function($matches) use (&$i) {
            $i += 1;
            return "Article $i";
        }, $string);
        $this->assertEquals($replacement, 'Article 1, Article 2, Article 3');
    }
}
