<?php

namespace App\Http\Controllers;

use App\Support\SettingsMenu;

/** v14: Sozlamalar bosh sahifasi - guruhlangan kartalar. */
class SettingsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('settings.branch') || auth()->user()->canany(['courses.view', 'courses.manage']), 403);

        return view('settings.index', ['groups' => SettingsMenu::groups()]);
    }
}
