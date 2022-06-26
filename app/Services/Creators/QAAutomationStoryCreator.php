<?php

namespace App\Services\Creators;

use App\Services\Generators\QAAutomationStoryGenerator;
use App\Services\Generators\StoryGeneratorContract;

class QAAutomationStoryCreator extends StoryCreator
{
    /**
     * @return string|null
     */
    public function getStoryProjectName(): string
    {
        return QAAutomationStoryGenerator::$shortcutProjectName;
    }

    /**
     * @param array $storyData
     * @return StoryGeneratorContract
     */
    protected function createStory(array $storyData): StoryGeneratorContract
    {
        return new QAAutomationStoryGenerator($storyData);
    }
}
