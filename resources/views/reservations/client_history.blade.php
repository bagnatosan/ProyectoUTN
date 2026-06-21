@extends('layouts.app')

@section('title', 'Mis Reservas | ProyectoUTN')

@section('main_align', 'items-start')
@section('content_width', 'max-w-4xl')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div>
        <span class="auth-role-badge auth-role-badge-client">Cliente</span>
        <h1 class="text-2xl md:text-3xl font-extrabold mt-2">Mis reservas</h1>
        <p class="text-sm text-slate-400 mt-1">Turnos y pedidos que hiciste desde la plataforma.</p>
    </div>

    @if($reservations->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-800 bg-slate-900/40 p-12 text-center">
            <p class="text-slate-400 font-medium">Todavía no tenés reservas registradas.</p>
            <p class="text-sm text-slate-500 mt-2">Explorá los catálogos y reservá un producto o turno.</p>
            <a href="{{ route('dashboard') }}" class="inline-flex mt-6 auth-role-btn auth-role-btn-client auth-role-btn-inline px-6">
                Ver catálogos
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reservations as $reservation)
                @php
                    $statusLabels = [
                        'pending' => ['Pendiente', 'bg-amber-500/10 text-amber-700 border-amber-500/20'],
                        'confirmed' => ['Confirmada', 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20'],
                        'completed' => ['Completada', 'bg-slate-500/10 text-slate-600 border-slate-500/20'],
                        'cancelled' => ['Cancelada', 'bg-rose-500/10 text-rose-700 border-rose-500/20'],
                    ];
                    $status = $statusLabels[$reservation->status] ?? ['Desconocido', 'bg-slate-500/10 text-slate-600 border-slate-500/20'];
                    $isCancellable = $reservation->isCancellable();
                @endphp
                <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <p class="font-bold text-lg">{{ $reservation->product->name ?? 'Producto' }}</p>
                            <p class="text-sm text-slate-400 mt-1">
                                {{ $reservation->reservation_date->format('d/m/Y') }}
                                · {{ \Illuminate\Support\Str::of($reservation->reservation_time)->substr(0, 5) }}
                            </p>
                            @if($reservation->notes)
                                <p class="text-xs text-slate-500 mt-2">{{ $reservation->notes }}</p>
                            @endif
                            @if($reservation->status === 'cancelled' && $reservation->cancellation_reason)
                                <p class="text-xs text-rose-400 mt-2">Motivo: {{ $reservation->cancellation_reason }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex self-start px-2.5 py-1 rounded-full text-xs font-semibold border {{ $status[1] }}">
                                {{ $status[0] }}
                            </span>
                            @if($isCancellable)
                                <button type="button"
                                        class="cancel-reservation-btn inline-flex self-start px-2.5 py-1 rounded-full text-xs font-semibold border border-rose-500/20 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 transition-all cursor-pointer"
                                        data-reservation-id="{{ $reservation->id }}"
                                        data-product="{{ $reservation->product->name ?? 'Producto' }}"
                                        data-date="{{ $reservation->reservation_date->format('d/m/Y') }}"
                                        data-time="{{ \Illuminate\Support\Str::of($reservation->reservation_time)->substr(0, 5) }}">
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Cancel Modal -->
<div id="cancel-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="cancel-modal-title">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
        <h2 id="cancel-modal-title" class="text-lg font-bold text-white">Cancelar reserva</h2>
        <p class="text-sm text-slate-400 mt-2" id="cancel-modal-info"></p>

        <div class="mt-4">
            <label for="cancel-reason" class="block text-xs font-semibold text-slate-300 mb-1">Motivo de cancelación <span class="text-slate-500">(opcional)</span></label>
            <textarea id="cancel-reason" rows="3" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-rose-500/50 transition-all" placeholder="Contanos por qué cancelás..."></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <button type="button" id="cancel-modal-close" class="px-4 py-2 text-sm font-semibold text-slate-400 bg-slate-800 hover:bg-slate-700 rounded-xl transition-all cursor-pointer">
                Volver
            </button>
            <button type="button" id="cancel-modal-confirm" class="px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-all cursor-pointer">
                Sí, cancelar reserva
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('cancel-modal');
    var modalInfo = document.getElementById('cancel-modal-info');
    var modalClose = document.getElementById('cancel-modal-close');
    var modalConfirm = document.getElementById('cancel-modal-confirm');
    var cancelReason = document.getElementById('cancel-reason');
    var currentReservationId = null;

    document.querySelectorAll('.cancel-reservation-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentReservationId = this.getAttribute('data-reservation-id');
            var product = this.getAttribute('data-product');
            var date = this.getAttribute('data-date');
            var time = this.getAttribute('data-time');

            modalInfo.textContent = '¿Estás seguro de cancelar la reserva de "' + product + '" para el ' + date + ' a las ' + time + '?';
            cancelReason.value = '';
            modal.classList.remove('hidden');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        currentReservationId = null;
    }

    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    modalConfirm.addEventListener('click', function () {
        if (!currentReservationId) return;

        var formData = new FormData();
        formData.append('reason', cancelReason.value);

        fetch('/reservations/' + currentReservationId + '/cancel', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al cancelar la reserva.');
            }
        })
        .catch(function () {
            alert('Error de conexión. Intentalo de nuevo.');
        })
        .finally(function () {
            closeModal();
        });
    });
});
</script>
@endsection
