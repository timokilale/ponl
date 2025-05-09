<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/pnzLogo.jpg') }}" type="image/jpeg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Admin Navigation -->
        <nav x-data="{ open: false }" class="bg-indigo-800 border-b border-indigo-700">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-white">
                                <img src="{{ asset('images/pnzLogo.jpg') }}" alt="Logo" class="h-9 w-auto" />
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-white">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-white">
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.tasks.index')" :active="request()->routeIs('admin.tasks.*')" class="text-white">
                                {{ __('Tasks') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.task-completions.index')" :active="request()->routeIs('admin.task-completions.*')" class="text-white">
                                {{ __('Task Completions') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.withdrawals.index')" :active="request()->routeIs('admin.withdrawals.*')" class="text-white">
                                {{ __('Withdrawals') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions.*')" class="text-white">
                                {{ __('Transactions') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.vip-levels.index')" :active="request()->routeIs('admin.vip-levels.*')" class="text-white">
                                {{ __('VIP Levels') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.referrals.index')" :active="request()->routeIs('admin.referrals.*')" class="text-white">
                                {{ __('Referrals') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" class="text-white">
                                {{ __('Settings') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.social-links.index')" :active="request()->routeIs('admin.social-links.*')" class="text-white">
                                {{ __('Social Links') }}
                            </x-nav-link>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-700 hover:bg-indigo-600 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->username }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">
                                    {{ __('User Dashboard') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-indigo-700 focus:outline-none focus:bg-indigo-700 focus:text-white transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-white">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-white">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.tasks.index')" :active="request()->routeIs('admin.tasks.*')" class="text-white">
                        {{ __('Tasks') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.task-completions.index')" :active="request()->routeIs('admin.task-completions.*')" class="text-white">
                        {{ __('Task Completions') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.withdrawals.index')" :active="request()->routeIs('admin.withdrawals.*')" class="text-white">
                        {{ __('Withdrawals') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions.*')" class="text-white">
                        {{ __('Transactions') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.vip-levels.index')" :active="request()->routeIs('admin.vip-levels.*')" class="text-white">
                        {{ __('VIP Levels') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.referrals.index')" :active="request()->routeIs('admin.referrals.*')" class="text-white">
                        {{ __('Referrals') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" class="text-white">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.social-links.index')" :active="request()->routeIs('admin.social-links.*')" class="text-white">
                        {{ __('Social Links') }}
                    </x-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-indigo-700">
                    <div class="px-4">
                        <div class="font-medium text-base text-white">{{ Auth::user()->username }}</div>
                        <div class="font-medium text-sm text-indigo-300">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('dashboard')" class="text-white">
                            {{ __('User Dashboard') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-white">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
