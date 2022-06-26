<?php

namespace App\Services\Creators;

use App\Services\Generators\QAStoryGenerator;
use App\Services\Generators\StoryGeneratorContract;

abstract class StoryCreator
{
    /** @var string */
    const NAMESPACE = __NAMESPACE__;

    /**
     * @return string|null
     */
    public abstract function getStoryProjectName(): string;

    /**
     * @param array $storyData
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generate(array $storyData): ?array
    {
        $story = $this->createStory($storyData);

        return $story->generate();
    }

    /**
     * @return string
     */
    public static function getCreatorName(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    /**
     * @param array $storyData
     * @return QAStoryGenerator
     */
    protected abstract function createStory(array $storyData): StoryGeneratorContract;
}
