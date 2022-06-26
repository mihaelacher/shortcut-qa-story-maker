<?php

namespace App\Services;

use App\Services\Creators\StoryCreator;
use App\Utils\LogUtil;

class ShortcutQAStoriesCreatorService
{
    /**
     * @param array $devStoryIds
     * @param int $qaIterationId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createShortcutQAStories(array $devStoryIds, int $qaIterationId)
    {
        LogUtil::info('Creating Shortcut QA Stories started!');

        foreach ($devStoryIds as $storyId => $selection) {
            $storyData = ShortcutResourceService::getStory($storyId);

            if (!is_null($storyData)) {
                $storyData['qa_iteration_id'] = $qaIterationId;
                $storyData['owner_ids'] = [$selection['owner_id']];

                self::generateShortcutQAStories($storyData, $selection['creators']);
            }
        }

        LogUtil::info('Creating Shortcut QA Stories finished!');
    }

    /**
     * @param array $storyData
     * @param array $qaStoryCreatorsArr
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function generateShortcutQAStories(array $storyData, array $qaStoryCreatorsArr)
    {
        LogUtil::info('---Creation of QA stories for DEV story: ' . $storyData['name'] . ' begins.');

        $storyId = $storyData['id'];
        $qaStoryCreatorsArr = array_fill_keys($qaStoryCreatorsArr, null);

        foreach ($qaStoryCreatorsArr as $creator => $QAStoryId) {
            $QAStoryId = self::generateShortcutQAStoryByType($creator, $storyData);

            $qaStoryCreatorsArr[$creator] = $QAStoryId;
        }

        ShortcutQAStoryLinkGeneratorService::generateShortcutQAStoryLinks($storyId, $qaStoryCreatorsArr);

        LogUtil::info('---Creation of QA stories for DEV story: ' . $storyData['name'] . ' finished.');
    }

    /**
     * @param string $storyCreator
     * @param array $storyData
     * @return ?int
     */
    private static function generateShortcutQAStoryByType(string $storyCreator, array $storyData): ?int
    {
        try {
            $storyCreatorPath = StoryCreator::NAMESPACE . '\\' . $storyCreator;
            /** @var StoryCreator $creator */
            $creator = new $storyCreatorPath();
            $qaStory = $creator->generate($storyData);

            $qaStoryId = is_null($qaStory) ? null : $qaStory['id'];

            self::logQAStoryCreationMsg($qaStoryId, $creator->getStoryProjectName());

            return $qaStoryId;
        } catch (\Throwable $t) {
            LogUtil::info('------ GENERATING STORY ' . $storyData['name'] . ' FOR '
                . $creator->getStoryProjectName() . ' FAILED! ERROR: ' . $t->getMessage() . ' -------');
        }

        return null;
    }

    /**
     * @param int|null $storyId
     * @param string $generator
     * @return void
     */
    private static function logQAStoryCreationMsg(?int $storyId, string $generator): void
    {
        if (!is_null($storyId)) {
            LogUtil::info('------' . $generator . ' successfully created.');
        } else {
            LogUtil::error('------' . $generator . ' creation failed.');
        }
    }
}
