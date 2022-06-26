<?php

namespace App\Services\Creators;

use App\Services\Generators\QAMasterStoryGenerator;
use App\Services\Generators\StoryGeneratorContract;

class QAMasterStoryCreator extends StoryCreator
{
    /**
     * @return string|null
     */
    public function getStoryProjectName(): string
    {
        return QAMasterStoryGenerator::$shortcutProjectName;
    }

    /**
     * @param array $storyData
     * @return StoryGeneratorContract
     */
    protected function createStory(array $storyData): StoryGeneratorContract
    {
        return new QAMasterStoryGenerator($storyData);
    }
}
