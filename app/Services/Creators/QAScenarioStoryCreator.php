<?php

namespace App\Services\Creators;

use App\Services\Generators\QAScenarioStoryGenerator;
use App\Services\Generators\StoryGeneratorContract;

class QAScenarioStoryCreator extends StoryCreator
{
    /**
     * @return string|null
     */
    public function getStoryProjectName(): string
    {
        return QAScenarioStoryGenerator::$shortcutProjectName;
    }

    /**
     * @param array $storyData
     * @return StoryGeneratorContract
     */
    protected function createStory(array $storyData): StoryGeneratorContract
    {
        return new QAScenarioStoryGenerator($storyData);
    }
}
