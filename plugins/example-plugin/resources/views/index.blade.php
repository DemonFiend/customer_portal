@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Example Plugin - Dashboard</div>
                <div class="card-body">
                    <h5>Welcome to the Example Plugin!</h5>
                    <p>This is a demonstration of the customer portal's plugin system.</p>

                    <div class="mt-3">
                        <h6>Plugin Configuration:</h6>
                        @if(isset($exampleConfig))
                        <ul>
                            <li>Feature Enabled: {{ $exampleConfig['feature_enabled'] ? 'Yes' : 'No' }}</li>
                            <li>Max Items: {{ $exampleConfig['max_items'] }}</li>
                            <li>Theme: {{ $exampleConfig['theme'] }}</li>
                        </ul>
                        @endif
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('example-plugin.create') }}" class="btn btn-primary">Create New Item</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection