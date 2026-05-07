@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $activeTab = null;
    
    // This establishes the hierarchy of which tab opens FIRST.
    // It checks top-to-bottom. Change this order if you want a different default priority.
    if ($user->hasAnyRole(['Super Admin'])) { $activeTab = 'super-admin'; }
    elseif ($user->hasAnyRole(['Validator'])) { $activeTab = 'validator'; }
    elseif ($user->hasAnyRole(['Department Head'])) { $activeTab = 'dept'; }
    elseif ($user->hasAnyRole(['Verifier'])) { $activeTab = 'verifier'; }
    elseif ($user->hasAnyRole(['Reports Viewing'])) { $activeTab = 'reports'; }
@endphp


<div class="container-fluid">

    @if($user->roles()->where('rol_active', 1)->count() > 1)
    <ul class="nav nav-pills mb-4" id="roleTabs" role="tablist">
        
        @if($user->hasAnyRole(['Super Admin']))
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'super-admin' ? 'active' : '' }}" id="super-admin-tab" data-bs-toggle="pill" data-bs-target="#super-admin-view" type="button" role="tab">Administrator Desk</button>
        </li>
        @endif

        @if($user->hasAnyRole(['Validator']))
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'validator' ? 'active' : '' }}" id="validator-tab" data-bs-toggle="pill" data-bs-target="#validator-view" type="button" role="tab">Validation Desk</button>
        </li>
        @endif

        @if($user->hasAnyRole(['Department Head']))
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'dept' ? 'active' : '' }}" id="dept-tab" data-bs-toggle="pill" data-bs-target="#dept-view" type="button" role="tab">Department View</button>
        </li>
        @endif

        @if($user->hasAnyRole(['Verifier']))
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'verifier' ? 'active' : '' }}" id="verifier-tab" data-bs-toggle="pill" data-bs-target="#verifier-view" type="button" role="tab">Verification Desk</button>
        </li>
        @endif

        @if($user->hasAnyRole(['Reports Viewing']))
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'reports' ? 'active' : '' }}" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports-view" type="button" role="tab">Reports Desk</button>
        </li>
        @endif
        
    </ul>
    @endif

    <div class="tab-content" id="roleTabsContent">
        
        @if($user->hasAnyRole(['Super Admin']))
        <div class="tab-pane fade {{ $activeTab === 'super-admin' ? 'show active' : '' }}" id="super-admin-view" role="tabpanel">
            @include('components.super-admin')
        </div>
        @endif

        @if($user->hasAnyRole(['Validator']))
        <div class="tab-pane fade {{ $activeTab === 'validator' ? 'show active' : '' }}" id="validator-view" role="tabpanel">
            @include('components.validator')
        </div>
        @endif

        @if($user->hasAnyRole(['Department Head']))
        <div class="tab-pane fade {{ $activeTab === 'dept' ? 'show active' : '' }}" id="dept-view" role="tabpanel">
            @include('components.department-head')
        </div>
        @endif

        @if($user->hasAnyRole(['Verifier']))
        <div class="tab-pane fade {{ $activeTab === 'verifier' ? 'show active' : '' }}" id="verifier-view" role="tabpanel">
            @include('components.verifier')
        </div>
        @endif

        @if($user->hasAnyRole(['Reports Viewing']))
        <div class="tab-pane fade {{ $activeTab === 'reports' ? 'show active' : '' }}" id="reports-view" role="tabpanel">
            @include('components.reports')
        </div>
        @endif

    </div>
</div>
@endsection