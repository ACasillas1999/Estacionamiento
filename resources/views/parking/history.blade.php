<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Movimientos</title>
    <style>
        :root{--bg:#0b1120;--panel:rgba(15,23,42,.78);--panel-soft:rgba(30,41,59,.6);--border:rgba(148,163,184,.18);--text:#e5eefb;--muted:#8fa1ba;--blue:#3b82f6;--green:#10b981;--amber:#f59e0b;--radius:18px;--shadow:0 18px 50px rgba(0,0,0,.28);}
        *{box-sizing:border-box} body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:radial-gradient(circle at top left,rgba(59,130,246,.18),transparent 28%),radial-gradient(circle at bottom right,rgba(16,185,129,.12),transparent 24%),var(--bg);color:var(--text);min-height:100vh}
        .page{max-width:1240px;margin:0 auto;padding:28px 18px 40px}.hero,.toolbar{display:flex;justify-content:space-between;gap:14px;align-items:end;flex-wrap:wrap}.hero{margin-bottom:22px}.hero h1{margin:0 0 8px;font-size:clamp(2rem,4vw,3rem)}.hero p{margin:0;color:var(--muted)}
        .panel{background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:20px}.toolbar{margin-bottom:18px}.search{display:flex;gap:10px;flex-wrap:wrap}.search input,.search button,.btn{font:inherit}.search input{min-width:280px;padding:12px 13px;border-radius:12px;border:1px solid var(--border);background:#0f172a;color:var(--text)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:12px 14px;cursor:pointer;text-decoration:none;font-weight:600}.btn-primary{background:var(--blue);color:white}.btn-secondary{background:rgba(148,163,184,.14);color:var(--text)}
        .summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:18px}.metric{padding:16px;border-radius:14px;background:var(--panel-soft);border:1px solid var(--border)}.metric small{display:block;color:var(--muted);text-transform:uppercase;letter-spacing:.07em}.metric strong{display:block;font-size:1.7rem;margin-top:8px}
        .table-wrap{overflow:auto} table{width:100%;border-collapse:collapse} th,td{padding:12px 10px;text-align:left;border-bottom:1px solid var(--border)} th{font-size:.76rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em} td small{color:var(--muted)} .status{display:inline-flex;padding:6px 10px;border-radius:999px;font-size:.76rem;font-weight:600}.status.open{background:rgba(245,158,11,.12);color:var(--amber)}.status.closed{background:rgba(16,185,129,.12);color:var(--green)}
        .pagination{margin-top:18px}.pagination nav{display:flex;justify-content:center}.pagination svg{width:18px}
        @media (max-width:640px){.page{padding:18px 12px 32px}.search input{min-width:0;width:100%}}
    </style>
</head>
<body>
    <div class="page">
        <section class="hero">
            <div>
                <h1>Historial de Movimientos</h1>
                <p>Consulta entradas y salidas, y filtra por placas, conductor, tipo de vehiculo o cajon.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al tablero</a>
        </section>

        <section class="panel">
            <div class="toolbar">
                <form method="GET" action="{{ route('history.index') }}" class="search">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por placas, conductor, vehiculo o cajon">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    @if ($search !== '')
                        <a href="{{ route('history.index') }}" class="btn btn-secondary">Limpiar</a>
                    @endif
                </form>
                <div style="color:var(--muted)">Registros: {{ $sessions->total() }}</div>
            </div>

            <div class="summary">
                <article class="metric">
                    <small>Mostrando</small>
                    <strong>{{ $sessions->count() }}</strong>
                </article>
                <article class="metric">
                    <small>Con salida</small>
                    <strong>{{ $sessions->whereNotNull('exit_time')->count() }}</strong>
                </article>
                <article class="metric">
                    <small>En curso</small>
                    <strong>{{ $sessions->whereNull('exit_time')->count() }}</strong>
                </article>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Vehiculo</th>
                            <th>Cajon</th>
                            <th>Conductor</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Tarifa</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                            <tr>
                                <td>
                                    <strong>{{ $session->plate_number }}</strong><br>
                                    <small>{{ $session->vehicle_type }}</small>
                                </td>
                                <td>{{ $session->parkingSpot?->code ?? 'N/D' }}</td>
                                <td>{{ $session->driver_name ?: 'Sin registro' }}</td>
                                <td>{{ $session->entry_time->format('d/m/Y H:i') }}</td>
                                <td>{{ $session->exit_time?->format('d/m/Y H:i') ?? 'Pendiente' }}</td>
                                <td>${{ number_format((float) $session->hourly_rate, 2) }}</td>
                                <td>${{ number_format((float) ($session->total_amount ?? 0), 2) }}</td>
                                <td>
                                    <span class="status {{ $session->exit_time ? 'closed' : 'open' }}">
                                        {{ $session->exit_time ? 'Cerrado' : 'En curso' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;color:var(--muted);padding:22px">No se encontraron movimientos con ese criterio.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $sessions->links() }}
            </div>
        </section>
    </div>
</body>
</html>
