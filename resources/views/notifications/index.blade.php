@extends('layouts.app')

@section('title', 'Notificaciones | Cocinet')

@section('main_align', 'items-start justify-center')
@section('content_width', 'max-w-3xl')

@section('content')
<div class="space-y-6 animate-fade-in">
  {{-- Header centrado --}}
  <div style="text-align:center;margin-bottom:0.5rem;">
    <h1 style="font-size:1.75rem;font-weight:800;color:#1a1918;letter-spacing:-0.02em;">Notificaciones</h1>
    <p style="font-size:0.9rem;color:#6a6966;margin-top:0.375rem;">Mantenete al día con el estado de tus reservas.</p>
    @if(auth()->user()->unreadNotifications->isNotEmpty())
      <div style="margin-top:1rem;margin-bottom:0.5rem;">
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display:inline;">
          @csrf
          <button type="submit" style="font-size:0.75rem;font-weight:600;color:#2d8c4e;background:rgba(45,140,78,0.1);border:1px solid rgba(45,140,78,0.25);border-radius:0.5rem;padding:0.4rem 0.9rem;cursor:pointer;transition:all 0.2s;">
            Marcar todas como leídas
          </button>
        </form>
      </div>
    @endif
  </div>

  @php
    $notifications = auth()->user()->notifications()->paginate(20);
  @endphp

  @if($notifications->isEmpty())
    <div style="border-radius:1rem;border:1.5px dashed #e8e0d0;background:#f9f7f2;padding:3rem;text-align:center;">
      <svg style="width:3rem;height:3rem;margin:0 auto 1rem;color:#b8b0a0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
      </svg>
      <p style="color:#6a6966;font-weight:500;">No tenés notificaciones.</p>
    </div>
  @else
    <div style="display:flex;flex-direction:column;gap:0.75rem;margin-top:1.25rem;">
      @foreach($notifications as $notification)
        @php
          $data = $notification->data;
          $isUnread = is_null($notification->read_at);
        @endphp
        <div style="border-radius:0.75rem;border:1.5px solid {{ $isUnread ? 'rgba(245,166,35,0.4)' : '#e8e0d0' }};background:{{ $isUnread ? 'rgba(245,166,35,0.06)' : '#ffffff' }};padding:1rem;transition:all 0.2s;">
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
              <p style="font-size:0.875rem;font-weight:{{ $isUnread ? '600' : '500' }};color:#1a1918;">
                {{ $data['message'] ?? 'Notificación' }}
              </p>
              @if(!empty($data['date']) && !empty($data['time']))
                <p style="font-size:0.75rem;color:#6a6966;margin-top:0.25rem;">{{ $data['date'] }} - {{ $data['time'] }}</p>
              @endif
              <p style="font-size:0.7rem;color:#9a9390;margin-top:0.125rem;">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              @if($isUnread)
                <button type="button"
                        class="notif-mark-read"
                        style="font-size:0.7rem;font-weight:600;color:#2d8c4e;background:rgba(45,140,78,0.1);border:1px solid rgba(45,140,78,0.25);border-radius:0.5rem;padding:0.25rem 0.6rem;cursor:pointer;transition:all 0.2s;"
                        data-notif-id="{{ $notification->id }}">
                  Leída
                </button>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $notifications->links() }}
    </div>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.notif-mark-read').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.getAttribute('data-notif-id');
      var card = this.closest('.rounded-xl');

      fetch('/notifications/' + id + '/mark-as-read', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          card.classList.remove('bg-indigo-500/5', 'border-indigo-500/30');
          card.classList.add('bg-slate-900/40', 'border-slate-800');
          card.querySelector('.notif-mark-read').remove();
        }
      });
    });
  });
});
</script>
@endsection
