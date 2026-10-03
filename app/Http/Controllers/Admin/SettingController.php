<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingsRepository;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const GROUPS = ['general', 'store', 'payment', 'tax', 'whatsapp', 'social', 'seo'];

    public function edit(string $group = 'general')
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        return view('admin.settings.edit', [
            'group' => $group,
            'groups' => self::GROUPS,
            'settings' => Setting::where('group', $group)->orderBy('key')->get(),
        ]);
    }

    public function update(Request $request, string $group, SettingsRepository $repo)
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        $values = $request->input('settings', []);
        $toggles = $request->input('bools', []);

        foreach (Setting::where('group', $group)->get() as $setting) {
            if ($setting->type === 'bool') {
                $repo->set($setting->key, in_array($setting->key, $toggles), $group, 'bool');
            } elseif (array_key_exists($setting->key, $values)) {
                $repo->set($setting->key, $values[$setting->key], $group, $setting->type);
            }
        }

        return back()->with('success', ucfirst($group).' settings saved.');
    }
}
