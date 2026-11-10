@extends('marketing.layout')

@section('title', 'Pricing — ElectricalInvoiceLoop Pilot Plans')

@section('content')
<section class="section">
    <h2>Pilot pricing</h2>
    <p class="lead">Hypothesis for electrical contractor owners and operations leads: Pilot $199–$799/mo depending on seats and integrations.</p>
    <div class="grid">
        <div class="card">
            <h3>Starter · $199/mo</h3>
            <p>AI invoice drafts, reminders, job tickets, 3 seats, email support.</p>
        </div>
        <div class="card">
            <h3>Growth · $449/mo</h3>
            <p>Payment reconciliation, audit log, technician roster, 10 seats, QuickBooks bridge.</p>
        </div>
        <div class="card">
            <h3>Ops · $799/mo</h3>
            <p>Full module suite, integrations hub, attendance option, priority onboarding.</p>
        </div>
    </div>
</section>
@endsection
