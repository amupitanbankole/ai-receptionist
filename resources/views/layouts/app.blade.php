<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'AI Receptionist Sales Engine')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">

<div class="d-flex min-vh-100">

    {{-- Sidebar --}}
    <aside class="bg-dark text-white p-3" style="width: 260px;">

        <div class="mb-4">
            <h4 class="fw-bold mb-1">AI Receptionist</h4>
            <small class="text-secondary">Sales Engine</small>
        </div>

        <hr class="border-secondary">

        <nav class="nav flex-column gap-1">

            <a href="{{ route('dashboard') }}"
               class="nav-link text-white rounded active">
                Dashboard
            </a>

            <div class="text-uppercase text-secondary small fw-bold mt-3 mb-1">
                Sales
            </div>

            <a href="{{ route('companies.index') }}" class="nav-link">
                Companies
            </a>

<a href="{{ route('contacts.index') }}" class="nav-link">                Contacts
            </a>

            <a href="#" class="nav-link text-white">
                Leads
            </a>

            <a href="#" class="nav-link text-white">
                Lead Scoring
            </a>

            <a href="#" class="nav-link text-white">
                Campaigns
            </a>

            <a href="#" class="nav-link text-white">
                Messages
            </a>

            <a href="#" class="nav-link text-white">
                Follow-ups
            </a>

            <div class="text-uppercase text-secondary small fw-bold mt-3 mb-1">
                Customers
            </div>

            <a href="#" class="nav-link text-white">
                Appointments
            </a>

            <a href="#" class="nav-link text-white">
                Customers
            </a>

            <a href="#" class="nav-link text-white">
                AI Conversations
            </a>

            <div class="text-uppercase text-secondary small fw-bold mt-3 mb-1">
                System
            </div>

            <a href="#" class="nav-link text-white">
                AI Receptionist
            </a>

            <a href="#" class="nav-link text-white">
                Settings
            </a>

        </nav>

    </aside>

    {{-- Main Content --}}
    <div class="flex-grow-1">

        {{-- Top Navigation --}}
        <header class="bg-white border-bottom px-4 py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-0">@yield('page_title', 'Dashboard')</h5>
                </div>

                <div class="d-flex align-items-center gap-3">

                    <button class="btn btn-light">
                        Notifications
                    </button>

                    <div class="dropdown">
                        <button
                            class="btn btn-outline-secondary dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                            Admin
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    Profile
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="#">
                                    Settings
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item" href="#">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </header>

        {{-- Page Content --}}
        <main class="p-4">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>