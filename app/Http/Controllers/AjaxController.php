<?php

namespace App\Http\Controllers;

use App\Services\ShortcutQAStoriesCreatorService;
use App\Services\ShortcutDashboardService;
use App\Utils\LogUtil;

class AjaxController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getShortcutDevStoriesTable() // TODO: add request class
    {
        return view('dev-stories-table')
            ->with('stories', ShortcutDashboardService::getShortcutDatatablesFormattedStories(request()->devStoryId))
            ->with('ninjaOwners', ShortcutDashboardService::getNinjaMembers());
    }

    /**
     * @return string|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createShortcutQAStories() // TODO: add request class
    {
        // TODO: instead of synchronous call here, start a cron instead, when additional configuration is available
        ini_set('max_execution_time', 300);

        $selectedStoryIds = json_decode(request()->selectedIds, true);
        $qaIterationId = request()->qaIterationId;

        if (empty($qaIterationId)) {
            LogUtil::error('No QA Iteration selected!');
            return;
        }

        ShortcutQAStoriesCreatorService::createShortcutQAStories($selectedStoryIds, $qaIterationId);
    }
}
