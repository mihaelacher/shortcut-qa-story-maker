<?php

namespace App\Http\Controllers;

use App\Services\ShortcutResourceService;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index() // TODO: add request class
    {
        $allIterations = array_column(ShortcutResourceService::getShortcutIterations(), 'name', 'id');

        krsort($allIterations);

        return view('dashboard')
            ->with('iterations', $allIterations);
    }
}
