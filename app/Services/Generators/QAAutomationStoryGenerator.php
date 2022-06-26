<?php

namespace App\Services\Generators;

class QAAutomationStoryGenerator extends QAStoryGenerator
{
    protected static string $qaShortcutStoryName = '[AUTOMATION]';
    public static string $shortcutProjectName = 'QA Automation';

    /**
     * @return null
     */
    protected function getIterationId()
    {
        // not needed in here
        return null;
    }
}
