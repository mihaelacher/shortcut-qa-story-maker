<?php

namespace App\Services\Generators;

use App\Services\ShortcutResourceService;

abstract class QAStoryGenerator implements StoryGeneratorContract
{
    /** @var array $storyData */
    private array $storyData;

    public function __construct(array $storyData)
    {
        $this->storyData = $storyData;
    }

    /**
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generate(): ?array
    {
        $storyData = $this->storyData;

        $qaStoryData = [
            'archived' => false,
            'name' => '[QA]' . static::$qaShortcutStoryName . ' ' . $storyData['name'],
            'story_type' => 'chore',
            'group_id' => StoryGeneratorSynchronizer::getQAGroupId(),
            'project_id' =>  StoryGeneratorSynchronizer::getShortcutProjects()[static::$shortcutProjectName],
            'owner_ids' => $storyData['owner_ids'],
            'description' => $storyData['description'],
            'iteration_id' => $this->getIterationId(),
        ];

        return ShortcutResourceService::createStory($qaStoryData);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getIterationId()
    {
        return $this->storyData['qa_iteration_id'];
    }
}
