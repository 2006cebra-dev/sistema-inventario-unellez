<style>
    .prestige-avatar { position: relative; padding: 3px; border-radius: 50%; display: inline-block; }
    .admin-frame { border: 4px solid #E50914; animation: rotate-frame 4s linear infinite; }
    @keyframes rotate-frame { 100% { transform: rotate(360deg); } }
</style>
<div class="prestige-avatar {{ auth()->user()->rol == 'admin' ? 'admin-frame' : '' }}">
    @if(auth()->user()->profile_photo_path)
        <img src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
    @else
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->display_name) }}" class="rounded-circle" width="40" height="40">
    @endif
</div>