@extends('marketing.layout')

@section('title', 'Workflow — ElectricalInvoiceLoop Billing Automation')

@section('content')
<section class="section">
    <h2>MVP workflow</h2>
    <p class="lead">1 intake trigger → draft/action → human approval → audit log. Built for electrical contractor finance teams.</p>
    <div class="steps">
        <div class="step"><h3>Intake</h3><p>Service request or completed job ticket enters the system with customer, hours, and materials.</p></div>
        <div class="step"><h3>AI draft</h3><p>ElectricalInvoiceLoop drafts an invoice (and later a reminder) using the AI hub workflow.</p></div>
        <div class="step"><h3>Human approval</h3><p>Owner or staff reviews totals, edits line items, and approves before send.</p></div>
        <div class="step"><h3>Collections</h3><p>Approved reminders go out only when messaging consent is verified.</p></div>
        <div class="step"><h3>Reconcile</h3><p>Payments update open balances; audit log captures every decision.</p></div>
    </div>
</section>
@endsection
