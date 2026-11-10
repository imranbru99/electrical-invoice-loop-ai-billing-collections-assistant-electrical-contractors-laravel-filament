@extends('marketing.layout')

@section('title', 'ElectricalInvoiceLoop — AI Billing & Collections Assistant for Electrical Contractors')

@section('content')
<section class="hero">
    <h1>ElectricalInvoiceLoop</h1>
    <p>AI billing & collections for electrical contractors — draft invoices from job tickets, reconcile payments, and send approved reminders with a human-in-the-loop audit trail.</p>
    <div class="cta-row">
        <a class="btn" href="{{ route('features') }}">Explore Features</a>
        <a class="btn btn-ghost" href="/admin">Open Admin Panel</a>
    </div>
</section>

<section class="section">
    <h2>Built for contractor cashflow</h2>
    <p class="lead">Intake a completed job ticket → AI drafts the invoice → owner approves → collections reminders go out only with messaging consent.</p>
    <div class="grid">
        <div class="card"><h3>Draft invoices</h3><p>Turn labor hours and materials into ready-to-approve invoices in seconds.</p></div>
        <div class="card"><h3>Reconcile payments</h3><p>Match checks, cards, and ACH to open balances automatically.</p></div>
        <div class="card"><h3>Approved reminders</h3><p>AI writes reminder copy; humans approve before any send.</p></div>
        <div class="card"><h3>Field operations</h3><p>Service requests, technicians, and job tickets feed the billing loop.</p></div>
    </div>
</section>

<section class="section">
    <h2>Modular by design</h2>
    <p class="lead">Enable only the modules your electrical business needs. Core billing stays on; optional hubs can stay off.</p>
    <div class="grid">
        @foreach(collect($modules)->take(8) as $module)
            <div class="card">
                <span class="badge {{ $module['enabled'] ? 'badge-on' : 'badge-off' }}">{{ $module['enabled'] ? 'ON' : 'OFF' }}</span>
                <h3>{{ $module['label'] }}</h3>
                <p>{{ $module['description'] }}</p>
            </div>
        @endforeach
    </div>
    <p style="margin-top:1.5rem"><a class="btn btn-ghost" href="{{ route('modules') }}">View all modules</a></p>
</section>
@endsection
