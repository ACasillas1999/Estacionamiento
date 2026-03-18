@php
    $selectedSpot = old('parking_spot_id') ? $spots->firstWhere('id', (int) old('parking_spot_id')) : null;
    $shouldOpenCheckInModal = $errors->checkIn->any() || old('parking_spot_id');
    $shouldOpenSpotModal = $errors->spot->any() || old('code') || old('zone');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estacionamiento A</title>
    <style>
        :root{--bg:#0b1120;--panel:rgba(15,23,42,.78);--panel-soft:rgba(30,41,59,.4);--border:rgba(148,163,184,.12);--text:#f8fafc;--muted:#94a3b8;--blue:#3b82f6;--green:#10b981;--amber:#f59e0b;--red:#ef4444;--radius:20px;--shadow:0 18px 50px rgba(0,0,0,.28);--side:#354867;--side-dark:#2a3b55;--side-line:rgba(255,255,255,.08);}
        *{box-sizing:border-box} body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:radial-gradient(circle at 0% 0%,rgba(59,130,246,.15),transparent 40%),radial-gradient(circle at 100% 100%,rgba(16,185,129,.1),transparent 40%),var(--bg);color:var(--text);min-height:100vh;line-height:1.5;overflow:hidden}
        .page{max-width:none;margin:0;padding:0}
        .hero{margin:0}.hero h1{margin:0 0 8px;font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-0.02em}.hero p{margin:0;color:var(--muted);font-size:1.1rem}
        .app-grid{display:grid;grid-template-columns:248px 1fr;gap:24px;align-items:start;height:100vh}
        .sidebar{display:grid;gap:0;position:sticky;top:0;height:100vh;background:linear-gradient(180deg,var(--side) 0%,var(--side-dark) 100%);border:1px solid var(--side-line);border-left:0;border-top:0;border-bottom:0;border-radius:0;overflow:hidden;box-shadow:var(--shadow)}
        .sidebar-brand{padding:16px 16px 12px;background:rgba(0,0,0,.14);border-bottom:1px solid var(--side-line)}
        .sidebar-kicker{display:flex;align-items:center;gap:10px;font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;color:#dbe7f7;font-weight:700}
        .sidebar-logo{width:22px;height:22px;border-radius:6px;background:linear-gradient(180deg,#1da1ff,#2563eb);box-shadow:inset 0 0 0 1px rgba(255,255,255,.14)}
        .sidebar-brand h2{margin:12px 0 6px;font-size:1.25rem;line-height:1.1}
        .sidebar-brand p{margin:0;color:#c4d3e8;font-size:.88rem;line-height:1.55}
        .sidebar-search{padding:12px 14px;border-bottom:1px solid var(--side-line)}
        .search-shell{position:relative}
        .search-shell input{width:100%;padding:11px 40px 11px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.08);background:rgba(0,0,0,.12);color:var(--text);font:inherit}
        .search-shell input::placeholder{color:#c4d3e8}
        .search-shell span{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#dbe7f7;font-weight:700}
        .sidebar-card{padding:0;border-top:1px solid var(--side-line)}
        .sidebar-actions{display:grid;grid-template-columns:1fr;gap:0}
        .side-action,.side-link{width:100%;border:0;background:transparent;color:var(--text);text-decoration:none;padding:14px 14px;display:grid;grid-template-columns:22px 1fr auto;align-items:center;gap:10px;text-align:left;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.05);position:relative}
        .side-action:hover,.side-link:hover{background:rgba(0,0,0,.12)}
        .side-action.is-active{background:rgba(0,0,0,.14)}
        .side-action.is-active::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:#16b2ff}
        .side-icon{display:grid;place-items:center;width:24px;height:24px;color:#dbe7f7;font-weight:700}
        .side-text strong{display:block;font-size:.95rem;font-weight:600}
        .side-text span{display:block;margin-top:2px;color:#c4d3e8;font-size:.76rem}
        .side-pill{padding:2px 8px;border-radius:999px;background:#6ee7b7;color:#0f172a;font-size:.7rem;font-weight:800}
        .side-arrow{color:#dbe7f7;font-size:1rem}
        .stats{display:grid;gap:0}
        .stats-title{padding:12px 14px 10px;color:#c4d3e8;text-transform:uppercase;letter-spacing:.08em;font-size:.72rem;border-bottom:1px solid rgba(255,255,255,.05)}
        .stat{background:transparent;border:0;border-radius:0;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid rgba(255,255,255,.05)}
        .stat:last-child{border-bottom:0}
        .stat:hover{background:rgba(0,0,0,.1)}
        .stat-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;flex-shrink:0}
        .stat-body{display:flex;align-items:center;gap:12px}
        .stat-info small{display:block;color:#c4d3e8;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:700}
        .stat-info strong{display:block;font-size:0.92rem;font-weight:600}
        .stat-value{font-size:1.35rem;font-weight:800}
        .stat-total .stat-icon{background:rgba(59,130,246,.15);color:var(--blue)}
        .stat-available .stat-icon{background:rgba(16,185,129,.15);color:var(--green)}
        .stat-occupied .stat-icon{background:rgba(245,158,11,.15);color:var(--amber)}
        .stat-inactive .stat-icon{background:rgba(239,68,68,.15);color:var(--red)}

        .main-shell{padding:24px 24px 24px 0;display:grid;gap:24px;height:100vh;overflow-y:auto;overflow-x:hidden}
        .main-shell::-webkit-scrollbar{width:10px}
        .main-shell::-webkit-scrollbar-thumb{background:rgba(148,163,184,.25);border-radius:999px}
        .panel,.modal-card{background:var(--panel);backdrop-filter:blur(12px);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:24px}
        .panel h2{margin:0 0 16px;font-size:1.25rem;font-weight:700;display:flex;align-items:center;gap:10px}
        .panel p{margin:0 0 20px;color:var(--muted);font-size:0.95rem}
        .content-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:24px;align-items:start}
        .tips,.vehicle-list{display:grid;gap:12px}.tip,.vehicle{padding:14px;border-radius:14px;background:var(--panel-soft);border:1px solid var(--border)}.tip strong{display:block;color:var(--text);margin-bottom:4px}
        .alert{padding:14px 16px;border-radius:14px;margin-bottom:18px}.alert.success{background:rgba(16,185,129,.14);color:#6ee7b7;border:1px solid rgba(16,185,129,.25)}.alert.error{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.24)}.alert.error ul{margin:8px 0 0 18px;padding:0}
        .form-grid{display:grid;gap:14px}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{display:grid;gap:6px}.field label{font-size:.85rem;color:var(--muted)} input,select,textarea,button{font:inherit} input,select,textarea{width:100%;padding:12px 13px;border-radius:12px;border:1px solid var(--border);background:#0f172a;color:var(--text)} textarea{min-height:86px;resize:vertical}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:12px 14px;cursor:pointer;transition:.18s ease;font-weight:600;text-decoration:none;text-align:center}.btn:hover{transform:translateY(-1px)}.btn-primary{background:var(--blue);color:white}.btn-secondary{background:rgba(148,163,184,.14);color:var(--text)}.btn-success{background:var(--green);color:white}.btn-ghost{background:transparent;color:var(--muted);border:1px solid var(--border)}
        .help-btn{width:44px;height:44px;padding:0;border-radius:999px;font-size:1.2rem;line-height:1;background:rgba(148,163,184,.14);color:var(--text)}
        .spots{display:grid;grid-template-columns:repeat(auto-fill,minmax(145px,1fr));gap:14px}.spot{width:100%;padding:16px 14px;border-radius:16px;border:1px solid var(--border);background:var(--panel-soft);text-align:center;color:var(--text)}.spot-code{font-size:1.35rem;font-weight:700}.spot-zone{margin:4px 0 12px;color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.06em}.badge{display:inline-flex;align-items:center;gap:8px;padding:7px 10px;border-radius:999px;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em}.badge::before{content:"";width:8px;height:8px;border-radius:999px;background:currentColor}.spot.free{border-top:3px solid var(--green);cursor:pointer}.spot.free:hover{border-color:rgba(59,130,246,.5);transform:translateY(-3px)}.spot.free .badge{background:rgba(16,185,129,.12);color:var(--green)}.spot.busy{border-top:3px solid var(--amber)}.spot.busy .badge{background:rgba(245,158,11,.12);color:var(--amber)}.spot.off{border-top:3px solid var(--red);opacity:.7}.spot.off .badge{background:rgba(239,68,68,.12);color:var(--red)}.spot-note{margin-top:10px;color:#7dd3fc;font-size:.78rem}.plate-chip{margin-top:10px;display:inline-block;padding:6px 10px;border-radius:10px;background:rgba(2,6,23,.5);border:1px dashed rgba(148,163,184,.25);font-family:Consolas,monospace;font-size:.82rem}
        .vehicles-panel{position:sticky;top:18px}.vehicle{display:flex;justify-content:space-between;align-items:center;gap:14px}.vehicle-id strong{display:block;font-family:Consolas,monospace;font-size:1.05rem}.vehicle-id span,.vehicle-meta{color:var(--muted);font-size:.9rem}.vehicle-empty{padding:20px;border-radius:14px;border:1px dashed var(--border);color:var(--muted);text-align:center}
        .modal[hidden]{display:none}.modal{position:fixed;inset:0;z-index:50;display:grid;place-items:center;padding:18px}.modal-backdrop{position:absolute;inset:0;background:rgba(2,6,23,.72);backdrop-filter:blur(6px)}.modal-card{position:relative;z-index:1;width:min(100%,640px);padding:20px}.modal-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:14px}.modal-head h3{margin:0 0 6px}.modal-head p{margin:0;color:var(--muted)}.selected{display:inline-flex;gap:8px;padding:8px 12px;border-radius:999px;background:rgba(59,130,246,.14);color:#93c5fd;margin-bottom:14px}
        @media (max-width:1100px){body{overflow:auto}.app-grid{grid-template-columns:1fr;height:auto}.sidebar{position:static;height:auto;border:1px solid var(--side-line);border-radius:0 0 22px 22px}.main-shell{padding:0 16px 24px;height:auto;overflow:visible}.content-grid{grid-template-columns:1fr}.vehicles-panel{position:static}}
        @media (max-width:640px){.main-shell{padding:0 12px 24px}.form-row{grid-template-columns:1fr}.vehicle{flex-direction:column;align-items:stretch;text-align:center}}
    </style>
</head>
<body>
    <div class="page">
        <section class="app-grid">
            <aside class="sidebar">
                @if (session('status'))
                    <div class="alert success">{{ session('status') }}</div>
                @endif

                <div class="sidebar-brand">
                    <div class="sidebar-kicker">
                        <span class="sidebar-logo"></span>
                        <span>Parking Navigation</span>
                    </div>
                    <h2>Estacionamiento A</h2>
                    <p>Entradas, salidas y monitoreo del mapa en una sola vista.</p>
                </div>

                <div class="sidebar-search">
                    <div class="search-shell">
                        <input type="text" placeholder="Buscar">
                        <span>Q</span>
                    </div>
                </div>

                <div class="sidebar-card">
                    <div class="sidebar-actions">
                        <button type="button" class="side-action is-active" data-open-help-modal>
                            <span class="side-icon">?</span>
                            <span class="side-text">
                                <strong>Dashboard</strong>
                                <span>Ayuda y guia del tablero</span>
                            </span>
                            <span class="side-arrow">></span>
                        </button>
                        <button type="button" class="side-action" data-open-spot-modal>
                            <span class="side-icon">+</span>
                            Agregar cajón
                        </button>
                        <a href="{{ route('history.index') }}" class="side-link">
                            <span class="side-icon">#</span>
                            <span class="side-text">
                                <strong>Historial</strong>
                                <span>Movimientos registrados</span>
                            </span>
                            <span class="side-arrow">></span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h2 style="font-size:0.9rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:16px">Estadísticas</h2>
                    <section class="stats">
                        <article class="stat stat-total">
                            <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg></div>
                            <div class="stat-info"><small>Total</small><strong>{{ $stats['total'] }}</strong></div>
                        </article>
                        <article class="stat stat-available">
                            <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
                            <div class="stat-info"><small>Libres</small><strong>{{ $stats['available'] }}</strong></div>
                        </article>
                        <article class="stat stat-occupied">
                            <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
                            <div class="stat-info"><small>Ocupados</small><strong>{{ $stats['occupied'] }}</strong></div>
                        </article>
                        <article class="stat stat-inactive">
                            <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></div>
                            <div class="stat-info"><small>Inactivos</small><strong>{{ $stats['inactive'] }}</strong></div>
                        </article>
                    </section>
                </div>
            </aside>

            <main class="main-shell">

                <section class="content-grid">
                <section class="panel">
                    <h2>Mapa de cajones</h2>
                    <p>Selecciona un cajon libre para registrar una nueva entrada.</p>
                    <div class="spots">
                        @forelse ($spots as $spot)
                            @php($statusClass = ! $spot->is_active ? 'off' : ($spot->activeSession ? 'busy' : 'free'))
                            @if (! $spot->is_active)
                                <div class="spot {{ $statusClass }}">
                                    <div class="spot-code">{{ $spot->code }}</div>
                                    <div class="spot-zone">{{ $spot->zone ?: 'Sin zona' }}</div>
                                    <div class="badge">Fuera</div>
                                </div>
                            @elseif ($spot->activeSession)
                                <div class="spot {{ $statusClass }}">
                                    <div class="spot-code">{{ $spot->code }}</div>
                                    <div class="spot-zone">{{ $spot->zone ?: 'Sin zona' }}</div>
                                    <div class="badge">Ocupado</div>
                                    <div class="plate-chip">{{ $spot->activeSession->plate_number }}</div>
                                </div>
                            @else
                                <button
                                    type="button"
                                    class="spot {{ $statusClass }} spot-trigger"
                                    data-spot-id="{{ $spot->id }}"
                                    data-spot-code="{{ $spot->code }}"
                                    data-spot-zone="{{ $spot->zone ?: 'Sin zona' }}"
                                >
                                    <div class="spot-code">{{ $spot->code }}</div>
                                    <div class="spot-zone">{{ $spot->zone ?: 'Sin zona' }}</div>
                                    <div class="badge">Libre</div>
                                    <div class="spot-note">Clic para registrar</div>
                                </button>
                            @endif
                        @empty
                            <p>No hay cajones registrados todavia.</p>
                        @endforelse
                    </div>
                </section>

                <section class="panel vehicles-panel">
                    <h2>Vehiculos dentro</h2>
                    <div class="vehicle-list">
                        @forelse ($occupiedSpots as $spot)
                            <article class="vehicle">
                                <div>
                                    <div class="vehicle-id">
                                        <strong>{{ $spot->activeSession->plate_number }}</strong>
                                        <span>Cajon {{ $spot->code }}</span>
                                    </div>
                                    <div class="vehicle-meta">
                                        {{ $spot->activeSession->vehicle_type }} - Entrada {{ $spot->activeSession->entry_time->format('H:i') }}
                                        @if ($spot->activeSession->driver_name)
                                            - {{ $spot->activeSession->driver_name }}
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('sessions.check-out', $spot->activeSession) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">Dar salida</button>
                                </form>
                            </article>
                        @empty
                        <div class="vehicle-empty">No hay vehiculos dentro en este momento.</div>
                    @endforelse
                </div>
                </section>
                </section>
            </main>
        </section>
    </div>

    <div class="modal" id="check-in-modal" @if (! $shouldOpenCheckInModal) hidden @endif>
        <div class="modal-backdrop" data-close-modal="check-in-modal"></div>
        <div class="modal-card">
            <div class="modal-head">
                <div>
                    <h3>Registrar entrada</h3>
                    <p>Completa los datos del vehiculo para el cajon seleccionado.</p>
                </div>
                <button type="button" class="btn btn-ghost" data-close-modal="check-in-modal">Cerrar</button>
            </div>

            @if ($errors->checkIn->any())
                <div class="alert error">
                    Revisa los datos de entrada.
                    <ul>
                        @foreach ($errors->checkIn->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="selected">
                <span>Cajon:</span>
                <strong id="selected-spot-label">{{ $selectedSpot?->code ? $selectedSpot->code.' / '.($selectedSpot->zone ?: 'Sin zona') : 'Selecciona un cajon libre' }}</strong>
            </div>

            <form method="POST" action="{{ route('sessions.check-in') }}" class="form-grid">
                @csrf
                <input type="hidden" id="parking_spot_id" name="parking_spot_id" value="{{ old('parking_spot_id') }}">

                <div class="form-row">
                    <div class="field">
                        <label>Placas</label>
                        <input type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="Ej. ABC-123" required>
                    </div>
                    <div class="field">
                        <label>Vehiculo</label>
                        <select name="vehicle_type" required>
                            <option value="Auto" @selected(old('vehicle_type') === 'Auto')>Auto</option>
                            <option value="Moto" @selected(old('vehicle_type') === 'Moto')>Moto</option>
                            <option value="Camioneta" @selected(old('vehicle_type') === 'Camioneta')>Camioneta</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label>Conductor</label>
                        <input type="text" name="driver_name" value="{{ old('driver_name') }}" placeholder="Nombre opcional">
                    </div>
                    <div class="field">
                        <label>Tarifa por hora</label>
                        <input type="number" name="hourly_rate" min="1" step="0.01" value="{{ old('hourly_rate', '25') }}" required>
                    </div>
                </div>

                <div class="field">
                    <label>Notas</label>
                    <textarea name="notes" placeholder="Observaciones del vehiculo...">{{ old('notes') }}</textarea>
                </div>

                <div class="form-row">
                    <button type="button" class="btn btn-ghost" data-close-modal="check-in-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar entrada</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="spot-modal" @if (! $shouldOpenSpotModal) hidden @endif>
        <div class="modal-backdrop" data-close-modal="spot-modal"></div>
        <div class="modal-card">
            <div class="modal-head">
                <div>
                    <h3>Agregar nuevo cajon</h3>
                    <p>Da de alta un nuevo espacio disponible dentro del estacionamiento.</p>
                </div>
                <button type="button" class="btn btn-ghost" data-close-modal="spot-modal">Cerrar</button>
            </div>

            @if ($errors->spot->any())
                <div class="alert error">
                    No se pudo crear el cajon.
                    <ul>
                        @foreach ($errors->spot->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('spots.store') }}" class="form-grid">
                @csrf
                <div class="form-row">
                    <div class="field">
                        <label>Codigo</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="Ej. A-01" required>
                    </div>
                    <div class="field">
                        <label>Zona</label>
                        <input type="text" name="zone" value="{{ old('zone') }}" placeholder="Ej. Planta Alta">
                    </div>
                </div>
                <div class="form-row">
                    <button type="button" class="btn btn-ghost" data-close-modal="spot-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear cajon</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="help-modal" hidden>
        <div class="modal-backdrop" data-close-modal="help-modal"></div>
        <div class="modal-card">
            <div class="modal-head">
                <div>
                    <h3>Ayuda del tablero</h3>
                    <p>Guia rapida para usar el control de accesos.</p>
                </div>
                <button type="button" class="btn btn-ghost" data-close-modal="help-modal">Cerrar</button>
            </div>

            <div class="tips">
                <div class="tip"><strong>Libre</strong>Haz clic en un cajon libre para abrir el modal y registrar placas, tipo de vehiculo y tarifa.</div>
                <div class="tip"><strong>Ocupado</strong>El cajon muestra la placa actual y la salida se registra desde la lista de vehiculos dentro.</div>
                <div class="tip"><strong>Fuera de servicio</strong>Ese cajon no acepta nuevas entradas hasta que vuelva a estar disponible.</div>
                <div class="tip"><strong>Historial</strong>Usa el boton de historial para consultar movimientos pasados sin saturar el tablero principal.</div>
            </div>
        </div>
    </div>

    <script>
        const checkInModal = document.getElementById('check-in-modal');
        const spotModal = document.getElementById('spot-modal');
        const helpModal = document.getElementById('help-modal');
        const spotIdInput = document.getElementById('parking_spot_id');
        const selectedSpotLabel = document.getElementById('selected-spot-label');
        const spotActionButton = document.querySelector('[data-open-spot-modal]');
        const sidebarStatsTitle = document.querySelector('.sidebar-card h2[style]');

        if (spotActionButton) {
            spotActionButton.innerHTML = `
                <span class="side-icon">+</span>
                <span class="side-text">
                    <strong>Agregar cajon</strong>
                    <span>Alta de nuevo espacio</span>
                </span>
                <span class="side-pill">New</span>
            `;
        }

        if (sidebarStatsTitle) {
            sidebarStatsTitle.outerHTML = '<div class="stats-title">Resumen actual</div>';
        }

        function syncBodyScroll() {
            const anyOpen = !checkInModal.hidden || !spotModal.hidden || !helpModal.hidden;
            document.body.style.overflow = anyOpen ? 'hidden' : '';
        }

        function openCheckInModal(id, code, zone) {
            spotIdInput.value = id;
            selectedSpotLabel.textContent = code + ' / ' + zone;
            checkInModal.hidden = false;
            syncBodyScroll();
        }

        function openSpotModal() {
            spotModal.hidden = false;
            syncBodyScroll();
        }

        function openHelpModal() {
            helpModal.hidden = false;
            syncBodyScroll();
        }

        function closeModal(modalId) {
            document.getElementById(modalId).hidden = true;
            syncBodyScroll();
        }

        document.querySelectorAll('.spot-trigger').forEach((button) => {
            button.addEventListener('click', () => {
                openCheckInModal(button.dataset.spotId, button.dataset.spotCode, button.dataset.spotZone);
            });
        });

        document.querySelectorAll('[data-open-spot-modal]').forEach((button) => {
            button.addEventListener('click', openSpotModal);
        });

        document.querySelectorAll('[data-open-help-modal]').forEach((button) => {
            button.addEventListener('click', openHelpModal);
        });

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', () => closeModal(button.dataset.closeModal));
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (!checkInModal.hidden) closeModal('check-in-modal');
                if (!spotModal.hidden) closeModal('spot-modal');
                if (!helpModal.hidden) closeModal('help-modal');
            }
        });

        syncBodyScroll();
    </script>
</body>
</html>
