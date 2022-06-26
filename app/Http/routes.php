<?php

Route::get('/', 'DashboardController@index');

Route::get('ajax/getShortcutDevStoriesTable', 'AjaxController@getShortcutDevStoriesTable');
Route::post('ajax/createShortcutQAStories', 'AjaxController@createShortcutQAStories');

