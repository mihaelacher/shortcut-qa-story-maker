<?php

namespace App\Services\Generators;

class QATestStoryGenerator extends QAStoryGenerator
{
    protected static string $qaShortcutStoryName = '[TESTING]';
    public static string $shortcutProjectName = 'QA Test';
}
