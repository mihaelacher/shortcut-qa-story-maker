<?php

namespace App\Services\Generators;

use App\Services\ShortcutResourceService;

class StoryGeneratorSynchronizer
{
    const QA_GROUP_NAME = 'Ninja';

    /** @var ?array $shortcutProjects */
    private ?array $shortcutProjects = null;
    /** @var ?string $qaShortcutGroupId */
    private ?string $qaShortcutGroupId = null;
    /** @var int|null $shortcutCurrentIterationId */
    private ?int $shortcutCurrentIterationId = null;

    /**
     * @var StoryGeneratorSynchronizer|null
     */
    private static ?StoryGeneratorSynchronizer $instance = null;

    private function __construct()
    {
    }

    /**
     * @return void
     */
    public static function flushState(): void
    {
        self::getInstance(true);
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getShortcutProjects()
    {
        $instance = self::getInstance();

        if ($instance->shortcutProjects === null) {
            $instance->shortcutProjects =
                array_column(ShortcutResourceService::getShortcutProjects(), 'id', 'name');
        }

        return $instance->shortcutProjects;
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getQAOwnerId()
    {
        $instance = self::getInstance();

        if ($instance->shortcutProjects === null) {
            $instance->shortcutProjects =
                array_column(ShortcutResourceService::getShortcutOwners(), 'id', 'name');
        }

        return $instance->shortcutOwners;
    }


    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getShortcutCurrentIterationId()
    {
        $instance = self::getInstance();

        if ($instance->shortcutCurrentIterationId === null) {
            $allIterations = array_column(ShortcutResourceService::getShortcutIterations(), 'id');

            sort($allIterations);

            $instance->shortcutCurrentIterationId = array_pop($allIterations);
        }

        return $instance->shortcutCurrentIterationId;
    }


    /**
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getQAGroupId()
    {
        $instance = self::getInstance();

        if ($instance->qaShortcutGroupId === null) {
            $groups = ShortcutResourceService::getShortcutGroups();

            $qaKey = array_search(self::QA_GROUP_NAME, array_column($groups, 'name'));

            $instance->qaShortcutGroupId = $groups[$qaKey]['id'];
        }

        return $instance->qaShortcutGroupId;
    }


    /**
     * @param bool $force
     * @return StoryGeneratorSynchronizer
     */
    private static function getInstance(bool $force = false): StoryGeneratorSynchronizer
    {
        if (self::$instance == null || $force) {
            self::$instance = new StoryGeneratorSynchronizer();
        }

        return self::$instance;
    }
}
