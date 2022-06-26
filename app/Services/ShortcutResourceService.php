<?php

namespace App\Services;

use App\Utils\LogUtil;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ShortcutResourceService
{
    const API_URL = 'https://api.app.shortcut.com/api/v3';
    const STORIES_URI = '/stories';
    const PROJECTS_URI = '/projects';
    const GROUPS_URI = '/groups';
    const WORKFLOW_URI = '/workflows';
    const ITERATION_URI = '/iterations';
    const LIST_ITERATION_STORIES_URI = '/iterations/%d/stories';
    const MEMBER_URI = '/members';
    const STORY_LINK_URI = '/story-links';

    const SHORTCUT_REQUEST_LIMIT = 200;

    /**
     * Returns a json array representing a story from Shortcut with the given id
     *
     * @param int|null $id Shortcut Story ID
     * @return array|null
     * @throws \Exception|GuzzleException
     */
    public static function getStory(int $id = null): ?array
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('Cannot get a story without passing an id.');
        }
        $story_id = self::STORIES_URI . '/' . $id;
        return self::sendShortcutRequest($story_id, 'GET');
    }

    /**
     * Returns a json array representing the created Shortcut story
     *
     * @param array|null $data Array of story parameters that are set when creating the story. Creating a story requires values for name and project_id
     * @return array|null
     * @throws \Exception|GuzzleException
     */
    public static function createStory(array $data = null): ?array
    {
        if (empty($data) || is_null($data['name'])) {
            LogUtil::error('Cannot create a story with an empty data array or without passing name and project_id arguments.');
            return null;
        }
        return self::sendShortcutRequest(self::STORIES_URI, 'POST', $data);
    }

    /**
     * Returns a json array representing a Shortcut story or null when a story is deleted or an error with the requests has occurred
     *
     * @param string $uri Uri for the specific type of data we want to manipulate
     * @param string $requestType One of the request types - POST|GET|PUT|DELETE
     * @param array|null $data Array of story parameters that are set when creating/updating the story
     * @return array|null
     * @throws GuzzleException
     */
    private static function sendShortcutRequest(string $uri, string $requestType, array $data = null): ?array
    {
        $requestEndpoint = self::getRequestEndpoint($uri);
        $client = new Client(['http_errors' => false]);

        $response = $client->request($requestType, $requestEndpoint, [
            'json' => $data
        ]);

        $responseStatusCode = $response->getStatusCode();

        if ($responseStatusCode > 300 || $requestType === 'DELETE') {
            return null;
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Returns the Shortcut request endpoint as a string having the specific type of data endpoint and the token of authentication in it
     *
     * @param string $uri Uri for the specific type of data we want to manipulate
     * @return string
     */
    private static function getRequestEndpoint(string $uri): string
    {
        return self::API_URL . $uri . '?token=' . self::getShortcutApiToken();
    }

    /**
     * Returns all `Shkolo` Shortcut Iterations in the form of json array
     *
     * @return array|null
     * @throws GuzzleException
     */
    public static function getShortcutIterations(): ?array
    {
        return self::sendShortcutRequest(self::ITERATION_URI, 'GET');
    }

    /**
     * Returns all `Shkolo` Shortcut Iteration's stories by iteration id in the form of json array
     *
     * @param int $iterationId
     * @return array|null
     * @throws GuzzleException
     */
    public static function listShortcutIterationStories(int $iterationId): ?array
    {
        return self::sendShortcutRequest(sprintf(self::LIST_ITERATION_STORIES_URI, $iterationId), 'GET');
    }

    /**
     * Returns all `Shkolo` Shortcut Projects in the form of json array
     *
     * @return array|null
     * @throws GuzzleException
     */
    public static function getShortcutProjects(): ?array
    {
        return self::sendShortcutRequest(self::PROJECTS_URI, 'GET');
    }


    /**
     * Returns all `Shkolo` Shortcut Members in the form of json array
     *
     * @return array|null
     * @throws GuzzleException
     */
    public static function getShortcutMembers(): ?array
    {
        return self::sendShortcutRequest(self::MEMBER_URI, 'GET');
    }


    /**
     * Returns all `Shkolo` Shortcut teams in the form of json array
     *
     * @return array|null
     * @throws GuzzleException
     */
    public static function getShortcutGroups(): ?array
    {
        return self::sendShortcutRequest(self::GROUPS_URI, 'GET');
    }

    /**
     * Returns all `Shkolo` Shortcut Workflows in the form of json array
     *
     * @return array|null
     * @throws GuzzleException
     */
    public static function getShortcutWorkflows(): ?array
    {
        return self::sendShortcutRequest(self::WORKFLOW_URI, 'GET');
    }

    /**
     *  Returns a json array representing the created Shortcut story link
     *
     * @param array|null $data
     * @return array|null
     * @throws GuzzleException
     */
    public static function createShortcutStoryLink(array $data = null): ?array
    {
        if (empty($data) || is_null($data['object_id'] || is_null($data['subject_id']) || is_null($data['verb']))) {
            LogUtil::error('Cannot create a story link with an empty data array or without passing object, subject id and verb arguments.');
            return null;
        }

        return self::sendShortcutRequest(self::STORY_LINK_URI, 'POST', $data);
    }

    /**
     * Due to Shortcut's request limitations, this method keeps track of sent requests
     * and replaces the used API token/stops request sending for the time left
     *
     * @return string
     */
    public static function getShortcutApiToken(): string
    {
        $counter = CacheUtilService::getShortcutRequestCounter();
        $apiTokens = CacheUtilService::getShortcutAPITokens();

        // CASE 1: Request limitation is not reached, increment counter
        if ($counter <= self::SHORTCUT_REQUEST_LIMIT) {
            if (empty($apiTokens)) {
                LogUtil::error('No shortcut API tokens in config file set!!');
                die();
            }

            CacheUtilService::replaceShortcutRequestCounter($counter++);
        }
        // CASE 2: Request limitation reached, remove first, already limited for 2 minutes API token
        else {
            array_pop($apiTokens);

            // if no available tokens are left, wait until the request limitation is over and start again
            if (empty($apiTokens)) {
                LogUtil::info('Shortcut request limit exceeded!');

                sleep(CacheUtilService::getShortcutRequestExpirationTime());
                CacheUtilService::clearShortcutAPITokensCache();

                return self::getShortcutApiToken();
            }

            CacheUtilService::replaceShortcutAPITokens($apiTokens);
            CacheUtilService::replaceShortcutRequestCounter(0);
        }

        // return first available api token
        return reset($apiTokens);
    }
}
