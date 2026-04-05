@extends('layouts.app')

@section('title', 'User Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">User Details</h2>
                    <p class="text-amber-100 text-sm mt-1">View user information and permissions</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('users.edit', $user) }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all">
                        Edit User
                    </a>
                    <a href="{{ route('users.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md">
            {{ session('success') }}
        </div>
        @endif

        <!-- User Information Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">User Information</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="ml-6">
                        <h4 class="text-xl font-bold text-gray-800">{{ $user->name }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Full Name</p>
                        <p class="text-md font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Email Address</p>
                        <p class="text-md font-semibold text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Phone Number</p>
                        <p class="text-md font-semibold text-gray-800">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Address</p>
                        <p class="text-md font-semibold text-gray-800">{{ $user->address ?? 'Not provided' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Member Since</p>
                        <p class="text-md font-semibold text-gray-800">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Last Updated</p>
                        <p class="text-md font-semibold text-gray-800">{{ $user->updated_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles and Permissions Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Roles & Permissions</h3>
                <p class="text-sm text-gray-500 mt-1">User's assigned roles and permissions</p>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Assigned Roles</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($user->roles as $role)
                            <span class="px-3 py-2 text-sm font-semibold rounded-lg bg-amber-100 text-amber-800">
                                {{ ucfirst($role->name) }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-500">No roles assigned</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Permissions ({{ $userPermissions->count() }})</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @forelse($userPermissions as $permission)
                            <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $permission->name)) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 col-span-full">No permissions assigned</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection