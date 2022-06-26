<?php

namespace App\Services\Generators;

class QAScenarioStoryGenerator extends QAStoryGenerator
{
    protected static string $qaShortcutStoryName = '[SCENARIO]';
    public static string $shortcutProjectName = 'QA Scenarios';
}
