@extends('marketing.layout')

@section('title', 'Demo Users — ElectricalInvoiceLoop')

@section('content')
<section class="section">
    <h2>Demo access</h2>
    <p class="lead">Seeded multi-role users for local Docker demos. Password for all accounts: <strong>password</strong></p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Role</th><th>Email</th><th>Access</th></tr>
            </thead>
            <tbody>
                <tr><td>Admin</td><td>admin@electricalinvoiceloop.test</td><td>Full admin panel</td></tr>
                <tr><td>Owner</td><td>owner@electricalinvoiceloop.test</td><td>Approvals & billing</td></tr>
                <tr><td>Staff</td><td>staff@electricalinvoiceloop.test</td><td>Day-to-day ops</td></tr>
                <tr><td>Technician</td><td>tech@electricalinvoiceloop.test</td><td>Field / tickets</td></tr>
                <tr><td>Customer</td><td>customer@electricalinvoiceloop.test</td><td>Portal role (no admin)</td></tr>
            </tbody>
        </table>
    </div>
    <p style="margin-top:1.5rem"><a class="btn" href="/admin/login">Go to Admin Login</a></p>
</section>
@endsection
