@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-5">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold mb-2">
                                Welcome back, {{ Auth::user()->display_name }}!
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-clock me-2"></i>
                                {{ now()->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="role-badge role-{{ Auth::user()->role }} text-dark">
                                {{ ucfirst(Auth::user()->role) }} Member
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h3 class="fw-bold mb-0">{{ $recentOrders->count() }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-shopping-bag text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Member Since</h6>
                            <h6 class="fw-bold mb-0">
                                {{ Auth::user()->user_registered->format('M d, Y') }}
                            </h6>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-calendar text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Member Benefits</h6>
                            <h6 class="fw-bold mb-0">
                                @switch(Auth::user()->role)
                                    @case('gold')
                                        20% off all products
                                        @break
                                    @case('silver')
                                        10% off all products
                                        @break
                                    @default
                                        Standard pricing
                                @endswitch
                            </h6>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-gem text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Recent Orders
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>#{{ $order->ID }}</td>
                                            {{-- <td>{{ $order->post_date->format('M d, Y') }}</td> --}}
                                            <td>{{ \Carbon\Carbon::parse($order->post_date)->format('M d, Y') }}</td>
                                            <td>
                                                @switch($order->post_status)
                                                    @case('wc-completed')
                                                        <span class="badge bg-success">Completed</span>
                                                        @break
                                                    @case('wc-processing')
                                                        <span class="badge bg-info">Processing</span>
                                                        @break
                                                    @case('wc-pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $order->post_status }}</span>
                                                @endswitch
                                            </td>
                                            <td>${{ number_format($order->order_total, 2) }}</td>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-3">No orders yet</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                                <i class="fas fa-store me-2"></i>Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Specific Content -->
    @if(Auth::user()->role === 'gold')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="me-4">
                                <i class="fas fa-crown fa-3x text-warning"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-warning mb-1">Gold Member Exclusive</h5>
                                <p class="mb-0">Enjoy 20% off on all products and priority support.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->role === 'silver')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="me-4">
                                <i class="fas fa-star fa-3x text-info"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-info mb-1">Silver Member Benefits</h5>
                                <p class="mb-0">You're enjoying 10% off on all products. Upgrade to Gold for 20% off!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
