<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ElectricalInvoiceLoop — AI Billing & Collections for Electrical Contractors')</title>
    <meta name="description" content="@yield('meta', 'AI billing and collections assistant for electrical contractors. Draft invoices, reconcile payments, send approved reminders, manage job tickets and field technicians.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/marketing.css') }}">
</head>
<body>
<header class="site-header">
    <a class="brand" href="{{ route('home') }}">ElectricalInvoiceLoop</a>
    <nav>
        <a href="{{ route('features') }}">Features</a>
        <a href="{{ route('workflow') }}">Workflow</a>
        <a href="{{ route('modules') }}">Modules</a>
        <a href="{{ route('pricing') }}">Pricing</a>
        <a href="{{ route('demo') }}">Demo</a>
        <a class="btn btn-sm" href="/admin">Admin Login</a>
    </nav>
</header>
<main>
    @yield('content')
</main>
<footer class="site-footer">
    <p>ElectricalInvoiceLoop — AI Billing & Collections Assistant for Electrical Contractors</p>
    <p>Built with Laravel + Filament · Modular AI business automation · <a href="https://imrandev.bd/contact">IMRAN Dev BD</a></p>
</footer>
</body>
</html>
