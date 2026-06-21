@extends('layouts.app')

@section('title', 'Notificaciones | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-3xl')

@section('content')
<div class="space-y-6 animate-fade-in">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Notificaciones</h1>
      <p class="text-sm text-slate-400 mt-1">Mantenete al día con el estado de tus reservas.</p>
    </div>
    @if(auth()->user()->unreadNotifications->isNotEmpty())
      <form action="{{ route('notifications.mark-all-read') }}" method="POST">
        @csrf
        <button type="submit" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 border border-indigo-500/20 rounded-lg px-3 py-1.5 transition-all">
          Marcar todas como leídas
        </button>
      </form>
    @endif
  </div>

  @php
    $notifications = auth()->user()->notifications()->paginate(20);
  @endphp

  @if($notifications->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-800 bg-slate-900/40 p-12 text-center">
      <svg class="w-12 h-12 mx-auto text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
      </svg>
      <p class="text-slate-400 font-medium">No tenés notificaciones.</p>
    </div>
  @else
    <div class="space-y-3">
      @foreach($notifications as $notification)
        @php
          $data = $notification->data;
          $isUnread = is_null($notification->read_at);
        @endphp
        <div class="rounded-xl border {{ $isUnread ? 'border-indigo-500/30 bg-indigo-500/5' : 'border-slate-800 bg-slate-900/40' }} p-4 transition-all">
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium {{ $isUnread ? 'text-white' : 'text-slate-300' }}">
                {{ $data['message'] ?? 'Notificación' }}
              </p>
              @if(!empty($data['date']) && !empty($data['time']))
                <p class="text-xs text-slate-500 mt-1">{{ $data['date'] }} - {{ $data['time'] }}</p>
              @endif
              <p class="text-xs text-slate-500 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              @if($isUnread)
                <button type="button"
                        class="notif-mark-read text-xs font-semibold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2.5 py-1 transition-all"
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
