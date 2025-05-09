<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialLinkController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware is now applied in the routes file
    }

    /**
     * Display a listing of the social links.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $socialLinks = SocialLink::orderBy('platform')->get();

        return view('admin.social-links.index', compact('socialLinks'));
    }

    /**
     * Show the form for creating a new social link.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.social-links.create');
    }

    /**
     * Store a newly created social link in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'platform' => ['required', 'string', 'max:255', 'unique:social_links'],
            'url' => ['required', 'url', 'max:255'],
            'icon' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $socialLink = SocialLink::create([
            'platform' => $request->platform,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->is_active ?? true,
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'create_social_link',
            'description' => 'Created social link: ' . $socialLink->platform,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Social link created successfully.');
    }

    /**
     * Show the form for editing the specified social link.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\View\View
     */
    public function edit(SocialLink $socialLink)
    {
        return view('admin.social-links.edit', compact('socialLink'));
    }

    /**
     * Update the specified social link in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SocialLink $socialLink)
    {
        $request->validate([
            'platform' => ['required', 'string', 'max:255', Rule::unique('social_links')->ignore($socialLink->id)],
            'url' => ['required', 'url', 'max:255'],
            'icon' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $socialLink->update([
            'platform' => $request->platform,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->is_active ?? false,
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'update_social_link',
            'description' => 'Updated social link: ' . $socialLink->platform,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Social link updated successfully.');
    }

    /**
     * Remove the specified social link from storage.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SocialLink $socialLink)
    {
        $platform = $socialLink->platform;
        $socialLink->delete();

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'delete_social_link',
            'description' => 'Deleted social link: ' . $platform,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Social link deleted successfully.');
    }
}
