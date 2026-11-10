@extends('marketing.layout')

@section('title', 'Modules — Toggle Features On/Off | ElectricalInvoiceLoop')

@section('content')
<section class="section">
    <h2>Feature modules</h2>
    <p class="lead">Based on your electrical contracting business, run or stop any module. Core billing modules stay on by default.</p>
    <div class="grid">
        @foreach($modules as $module)
            <div class="card">
                <span class="badge {{ $module['enabled'] ? 'badge-on' : 'badge-off' }}">{{ $module['enabled'] ? 'ENABLED' : 'DISABLED' }}{{ $module['core'] ? ' · CORE' : '' }}</span>
                <h3>{{ $module['label'] }}</h3>
                <p>{{ $module['description'] }}</p>
                <p style="margin-top:.7rem;color:#7f93a8;font-size:.85rem">Key: {{ $module['key'] }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
