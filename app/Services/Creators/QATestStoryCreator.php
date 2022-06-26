<?php

namespace App\Services\Creators;

use App\Services\Generators\QATestStoryGenerator;
use App\Services\Generators\StoryGeneratorContract;

class QATestStoryCreator extends StoryCreator
{
    /**
     * @return string|null
     */
    public function getStoryProjectName(): string
    {
        return QATestStoryGenerator::$shortcutProjectName;
    }

    /**
     * @param array $storyData
     * @return StoryGeneratorContract
     */
    protected function createStory(array $storyData): StoryGeneratorContract
    {
        return new QATestStoryGenerator($storyData);
    }
}
