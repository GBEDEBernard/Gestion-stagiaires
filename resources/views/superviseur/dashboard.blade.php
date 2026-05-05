<xai:tool_usage_card>
    Superviseur Dashboard created: lists supervised stages, today's attendance, pending reports.
</xai:tool_usage_card>
@extends('layouts.app')

@section('title', 'Dashboard Superviseur')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-8">Dashboard Superviseur</h1>

            @if($supervisedStages->isEmpty())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Aucun stage actif</h3>
                <p>Vous n'avez pas de stages à superviser actuellement.</p>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($supervisedStages as $stage)
                <div class="bg-white border rounded-lg p-6 shadow-sm">
                    <h3 class="font-semibold text-lg mb-2">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $stage->service->name }} - {{ $stage->site->name }}</p>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span>Pointage aujourd'hui:</span>
                            <span class="{{ $stage->attendanceDays->first()?->check_in_time ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $stage->attendanceDays->first()?->check_in_time ? 'OK' : 'En attente' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Rapport du jour:</span>
                            <span class="text-green-600">{{ $stage->dailyReports->first()?->status ?? 'Non soumis' }}</span>
                        </div>
                    </div>
                    <a href="{{ encrypted_route('stages.show', $stage) }}" class="btn-primary w-full text-center">Voir stage</a>
                </div>
                @endforeach
            </div>
            @endif

            @if($pendingReviews->isNotEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-yellow-800 mb-4">Rapports en attente ({{ $pendingReviews->count() }})</h3>
                <ul class="space-y-2">
                    @foreach($pendingReviews->take(5) as $report)
                    <li class="flex justify-between items-center p-3 bg-white rounded">
                        <span>{{ $report->stage->etudiant->prenom }} {{ $report->stage->etudiant->nom }} - {{ $report->report_date->format('d/m') }}</span>
                        <a href="#" class="text-blue-600 hover:underline text-sm">Valider</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <div class="w-full md:w-80">
            <!-- Pointage rapide -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Mon pointage</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span>Aujourd'hui</span>
                        <span id="today-status" class="font-semibold">En attente</span>
                    </div>
                    <button id="checkin-btn" class="w-full bg-white/20 backdrop-blur-sm rounded p-3 text-sm font-medium hover:bg-white/30 transition-all">
                        Pointer arrivée
                    </button>
                    <button id="checkout-btn" class="w-full bg-white/20 backdrop-blur-sm rounded p-3 text-sm font-medium hover:bg-white/30 transition-all opacity-50" disabled>
                        Pointer départ
                    </button>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white border rounded-lg p-6">
                <h4 class="font-semibold mb-4">Actions rapides</h4>
                <div class="space-y-2">
                    <a href="{{ route('reports.index') }}" class="block p-3 border rounded hover:bg-gray-50 text-sm">
                        📋 Nouveau rapport journalier
                    </a>
                    <a href="{{ route('presence.index') }}" class="block p-3 border rounded hover:bg-gray-50 text-sm">
                        📍 Pointages détaillés
                    </a>
                    <a href="{{ route('admin.presence.index') }}" class="block p-3 border rounded hover:bg-gray-50 text-sm">
                        👥 Superviser équipe
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // JS pour pointage en temps reel (geolocation)
        const checkinBtn = document.getElementById('checkin-btn');
        const checkoutBtn = document.getElementById('checkout-btn');

        navigator.geolocation.getCurrentPosition(position => {
            // Update status based on current position vs site geofence
        });
    });
</script>
@endpush>