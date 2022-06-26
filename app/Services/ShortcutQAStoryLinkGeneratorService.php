<?php

namespace App\Services;

use App\Services\Creators\QAAutomationStoryCreator;
use App\Services\Creators\QAMasterStoryCreator;
use App\Services\Creators\QAScenarioStoryCreator;
use App\Services\Creators\QATestStoryCreator;
use App\Utils\LogUtil;

class ShortcutQAStoryLinkGeneratorService
{
    /**
     * @param int|null $devStoryId
     * @param array $qaStoryGeneratorsArr
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function generateShortcutQAStoryLinks(int $devStoryId, array $qaStoryGeneratorsArr)
    {
        try {
            // TODO: could be optimized
            $masterStoryId = self::getGeneratedQAStoryId($qaStoryGeneratorsArr, QAMasterStoryCreator::getCreatorName());
            $testStoryId = self::getGeneratedQAStoryId($qaStoryGeneratorsArr, QATestStoryCreator::getCreatorName());
            $scenarioStoryId = self::getGeneratedQAStoryId($qaStoryGeneratorsArr, QAScenarioStoryCreator::getCreatorName());
            $automationStoryId = self::getGeneratedQAStoryId($qaStoryGeneratorsArr, QAAutomationStoryCreator::getCreatorName());

            // original story relates to Master Story
            self::createShortcutStoryLink($masterStoryId, $devStoryId, 'relates to');
            // original story is blocked by Test Story
            self::createShortcutStoryLink($devStoryId, $testStoryId, 'blocks');
            // Master Story is blocked by Scenario Story
            self::createShortcutStoryLink($masterStoryId, $scenarioStoryId, 'blocks');
            // Master Story is blocked by Test Story
            self::createShortcutStoryLink($masterStoryId, $testStoryId, 'blocks');
            // Master Story is blocked by Automation Story
            self::createShortcutStoryLink($masterStoryId, $automationStoryId, 'blocks');
            // Test Story is blocked by Scenario Story
            self::createShortcutStoryLink($testStoryId, $scenarioStoryId, 'blocks');
            // Automation Story is blocked by Scenario Story
            self::createShortcutStoryLink($automationStoryId, $scenarioStoryId, 'blocks');
            // Automation Story is blocked by Test Story
            self::createShortcutStoryLink($automationStoryId, $testStoryId, 'blocks');

        } catch (\Throwable $t) {
            LogUtil::info('------ GENERATING STORY LINKS FAILED! ERROR: ' . $t->getMessage() . ' -------');
        }

    }

    /**
     * @param array $qaStoryCreatorsArr
     * @param string $creator
     * @return mixed|null
     */
    private static function getGeneratedQAStoryId(array $qaStoryCreatorsArr, string $creator)
    {
        if (isset($qaStoryCreatorsArr[$creator])) {
            return $qaStoryCreatorsArr[$creator];
        }

        return null;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function createShortcutStoryLink(?int $objectId, ?int $subjectId, string $verb): void
    {
        if (is_null($objectId) || is_null($subjectId)) {
            return;
        }

        $storyLinkData = [
            'object_id' => $objectId,
            'subject_id' => $subjectId,
            'verb' => $verb,
        ];

        ShortcutResourceService::createShortcutStoryLink($storyLinkData);
    }
}
