<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = Setting::orderBy('key')->get();

        // Group settings by category
        $groupedSettings = [
            'general' => [],
            'payment' => [],
            'withdrawal' => [],
            'referral' => [],
            'task' => [],
            'vip' => [],
            'other' => [],
        ];

        foreach ($settings as $setting) {
            $key = $setting->key;

            if (strpos($key, 'payment_') === 0 || strpos($key, 'deposit_') === 0 || strpos($key, 'coinbase_') === 0) {
                $groupedSettings['payment'][] = $setting;
            } elseif (strpos($key, 'withdrawal_') === 0) {
                $groupedSettings['withdrawal'][] = $setting;
            } elseif (strpos($key, 'referral_') === 0) {
                $groupedSettings['referral'][] = $setting;
            } elseif (strpos($key, 'task_') === 0) {
                $groupedSettings['task'][] = $setting;
            } elseif (strpos($key, 'vip_') === 0) {
                $groupedSettings['vip'][] = $setting;
            } elseif (strpos($key, 'site_') === 0 || strpos($key, 'app_') === 0) {
                $groupedSettings['general'][] = $setting;
            } else {
                $groupedSettings['other'][] = $setting;
            }
        }

        return view('admin.settings.index', compact('groupedSettings'));
    }

    /**
     * Update the specified settings in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_settings',
            'description' => 'Updated system settings',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Show the form for creating a new setting.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Store a newly created setting in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:settings'],
            'value' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        Setting::setValue($request->key, $request->value, $request->description);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_setting',
            'description' => 'Created setting: ' . $request->key,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Show the form for editing the specified setting.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\View\View
     */
    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Remove the specified setting from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Setting $setting)
    {
        $settingKey = $setting->key;
        $setting->delete();

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_setting',
            'description' => 'Deleted setting: ' . $settingKey,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }
}
