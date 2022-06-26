<?php

namespace App\Services;

use App\Services\Generators\StoryGeneratorSynchronizer;

class ShortcutDashboardService
{
    const DEV_WORKFLOW_NAME = 'Development';

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getShortcutDatatablesFormattedStories(?int $iterationId): array
    {
        $iterationStories = self::getShortcutDevStoriesByIteration($iterationId);
        $shortcutWorkflows = ShortcutResourceService::getShortcutWorkflows();
        $devWorkflowKey = self::getDevWorkflowKey($shortcutWorkflows);
        $devWorkflowStates = self::getDevWorkflowStates($shortcutWorkflows, $devWorkflowKey);
        $shortcutGroups = ShortcutResourceService::getShortcutGroups();

        if (is_null($shortcutGroups)) {
            return [];
        }

        $shortcutGroups = array_column($shortcutGroups, 'name', 'id');
        $result = [];

        foreach ($iterationStories ?? [] as $iterationStory) {
            if ($iterationStory['workflow_id'] !== $shortcutWorkflows[$devWorkflowKey]['id']
                || $iterationStory['archived']) {
                continue;
            }

            $result[] = [
                'id' => $iterationStory['id'],
                'name' => $iterationStory['name'],
                'type' => $iterationStory['story_type'],
                'group' => $iterationStory['group_id'] ? $shortcutGroups[$iterationStory['group_id']] : '---',
                'labels' => self::getFormattedLabels($iterationStory['labels']),
                'state' => $devWorkflowStates[$iterationStory['workflow_state_id']],
                'owner_ids' => $iterationStory['owner_ids']
            ];
        }

        return $result;
    }

    /**
     * @param int|null $iterationId
     * @return ?array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function getShortcutDevStoriesByIteration(?int $iterationId): ?array
    {
        if (empty($iterationId)) {
            return null;
        }

        return ShortcutResourceService::listShortcutIterationStories($iterationId);
    }

    /**
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function getDevWorkflowKey(?array $workFlows)
    {
        if (is_null($workFlows)) {
            return null;
        }

        return array_search(self::DEV_WORKFLOW_NAME, array_column($workFlows, 'name'));;
    }

    /**
     * @param array|null $workflows
     * @param int|null $devWorkflowKey
     * @return array|null
     */
    private static function getDevWorkflowStates(?array $workflows, ?int $devWorkflowKey): ?array
    {
        if (is_null($workflows) || is_null($devWorkflowKey)) {
            return null;
        }

        return array_column($workflows[$devWorkflowKey]['states'], 'name', 'id');
    }

    /**
     * @param array $storyLabelsArr
     * @return string
     */
    private static function getFormattedLabels(array $storyLabelsArr): string
    {
        if (empty($storyLabelsArr)) {
            return '---';
        }

        return implode(', ', array_column($storyLabelsArr, 'name'));
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getNinjaMembers(): array
    {
        $shortcutGroups = ShortcutResourceService::getShortcutGroups();
        $qaKey = array_search(StoryGeneratorSynchronizer::QA_GROUP_NAME, array_column($shortcutGroups, 'name'));
        $ninjaGroupMembers = $shortcutGroups[$qaKey]['member_ids'];
        $shortcutMembers = ShortcutResourceService::getShortcutMembers();
        $ninjaMembers = [];

        foreach ($shortcutMembers as $shortcutMember) {
            if (in_array($shortcutMember['id'], $ninjaGroupMembers)) {
                $ninjaMembers[$shortcutMember['id']] = $shortcutMember['profile']['name'];
            }
        }

        return $ninjaMembers;
    }
}
