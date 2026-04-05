@extends('layouts.app')

@section('title', 'Reset User Passwords')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Reset User Passwords</h2>
                    <p class="text-amber-100 text-sm mt-1">Administrator password reset tool</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                    Back to Dashboard
                </a>
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

        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Select User to Reset Password</h3>
                <p class="text-sm text-gray-500 mt-1">Reset passwords for any user in the system</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($users as $user)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                                    <span class="text-white font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <button onclick="showResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')" 
                                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                            Reset Password
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Reset Password</h3>
                <button onclick="closeResetModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="resetForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="user_id" id="reset_user_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <input type="text" id="reset_user_name" readonly class="w-full rounded-lg border-gray-300 bg-gray-50 p-3">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="password" required class="w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 p-3">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 p-3">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeResetModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg">Reset Password</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showResetModal(userId, userName) {
        document.getElementById('reset_user_id').value = userId;
        document.getElementById('reset_user_name').value = userName;
        document.getElementById('resetForm').action = `/password/reset-user/${userId}`;
        document.getElementById('resetModal').classList.remove('hidden');
        document.getElementById('resetModal').classList.add('flex');
    }
    
    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
        document.getElementById('resetModal').classList.remove('flex');
    }
</script>
@endpush
@endsection