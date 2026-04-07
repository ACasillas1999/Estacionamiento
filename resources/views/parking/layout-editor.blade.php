<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editor de Plano</title>
    <style>
        :root{--bg:#09111f;--panel:rgba(14,23,40,.86);--panel-soft:rgba(30,41,59,.42);--line:rgba(148,163,184,.16);--text:#eef4ff;--muted:#9fb0c8;--blue:#3b82f6;--green:#10b981;--red:#ef4444;--radius:20px;--shadow:0 18px 50px rgba(0,0,0,.3)}
        *{box-sizing:border-box} body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:radial-gradient(circle at top left,rgba(59,130,246,.18),transparent 28%),radial-gradient(circle at bottom right,rgba(16,185,129,.1),transparent 24%),var(--bg);color:var(--text);min-height:100vh;overflow:hidden}
        .page{max-width:none;margin:0;padding:18px 18px 24px;height:100vh;display:grid;grid-template-rows:auto minmax(0,1fr);gap:16px;overflow:hidden}
        .hero{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;flex-wrap:wrap}
        .hero h1{margin:0 0 8px;font-size:clamp(1.8rem,3vw,2.7rem)}
        .hero p{margin:0;color:var(--muted);max-width:760px}
        .hero-actions{display:flex;gap:10px;flex-wrap:wrap}
        .editor-kicker{display:inline-flex;align-items:center;gap:8px;margin-bottom:10px;padding:6px 10px;border-radius:999px;background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.18);color:#a5c8ff;font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;font-weight:800}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:11px 14px;cursor:pointer;text-decoration:none;font-weight:600}
        .btn-primary{background:linear-gradient(180deg,#4f8dff,#2563eb);color:#fff;box-shadow:0 14px 24px rgba(37,99,235,.24)}
        .btn-secondary{background:rgba(148,163,184,.14);color:var(--text)}
        .btn-danger{background:rgba(239,68,68,.14);color:#fecaca}
        #layout-form{display:block;min-height:0;height:100%;overflow:hidden}
        .layout-grid{display:grid;grid-template-columns:340px minmax(0,1fr) 360px;gap:16px;align-items:stretch;height:100%;min-height:0}
        .layout-grid > *{min-height:0;height:100%}
        .stack{display:grid;gap:14px;min-height:0}
        .inspector-stack{display:flex;flex-direction:column;gap:14px;min-height:0;height:100%;max-height:100%;overflow-y:auto;overflow-x:hidden;padding-right:8px;padding-bottom:20px;overscroll-behavior:contain;scrollbar-gutter:stable;-webkit-overflow-scrolling:touch;scrollbar-width:thin;scrollbar-color:rgba(148,163,184,.6) transparent}
        .inspector-stack > *{flex-shrink:0}
        .workspace-stack{min-height:0;height:100%;overflow:hidden}
        .spots-sidebar{min-height:0;height:100%;overflow:hidden}
        .panel{background:linear-gradient(180deg,rgba(14,23,40,.9),rgba(10,18,32,.84));border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);padding:16px;min-height:0}
        .panel h2{margin:0 0 8px;font-size:1.08rem}
        .panel p{margin:0;color:var(--muted);font-size:.88rem;line-height:1.45}
        .inspector-overview{padding:18px;background:linear-gradient(180deg,rgba(21,34,58,.96),rgba(12,21,37,.92));border-color:rgba(96,165,250,.18)}
        .overview-kicker{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:rgba(59,130,246,.14);border:1px solid rgba(96,165,250,.2);color:#bfdbfe;font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;font-weight:800}
        .overview-kicker::before{content:"";width:10px;height:10px;border-radius:999px;background:linear-gradient(180deg,#60a5fa,#2563eb);box-shadow:0 0 0 5px rgba(59,130,246,.12)}
        .overview-title{margin:14px 0 6px;font-size:1.2rem;font-weight:800}
        .overview-copy{color:#bfd1e8;font-size:.9rem;line-height:1.55}
        .overview-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:14px}
        .overview-stat{padding:12px 10px;border-radius:16px;background:rgba(8,15,29,.56);border:1px solid rgba(148,163,184,.12)}
        .overview-stat strong{display:block;font-size:1.05rem;color:#fff}
        .overview-stat span{display:block;margin-top:4px;color:var(--muted);font-size:.72rem;letter-spacing:.08em;text-transform:uppercase}
        .inspector-panel{position:relative;overflow:hidden}
        .inspector-panel::before{content:"";position:absolute;inset:0 auto 0 0;width:3px;border-radius:999px;background:linear-gradient(180deg,rgba(96,165,250,.95),rgba(37,99,235,.1))}
        .panel-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:12px}
        .panel-head-copy{display:grid;gap:4px}
        .panel-label{display:inline-flex;align-items:center;gap:8px;margin-bottom:8px;padding:5px 9px;border-radius:999px;background:rgba(59,130,246,.1);border:1px solid rgba(96,165,250,.16);color:#a9c8ff;font-size:.66rem;letter-spacing:.14em;text-transform:uppercase;font-weight:800}
        .panel-head-actions{display:flex;gap:8px;flex-wrap:wrap}
        .meta-strip{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:12px}
        .meta-pill{display:grid;gap:4px;padding:11px 12px;border-radius:16px;background:rgba(8,15,29,.56);border:1px solid rgba(148,163,184,.12);color:#d9e5f6;font-size:.75rem}
        .meta-pill strong{font-size:1.05rem;color:#fff}
        .form-grid{display:grid;gap:12px}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
        .field{display:grid;gap:5px}
        .field label{font-size:.79rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
        input,select,button{font:inherit}
        input,select{width:100%;padding:10px 11px;border-radius:12px;border:1px solid var(--line);background:#0f172a;color:var(--text);box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
        .toggle{display:flex;align-items:center;gap:10px;padding:11px 12px;border:1px solid var(--line);border-radius:14px;background:linear-gradient(180deg,rgba(20,31,52,.92),rgba(12,21,37,.82))}.toggle input{width:auto}
        .alert{padding:13px 14px;border-radius:14px;background:rgba(16,185,129,.14);color:#6ee7b7;border:1px solid rgba(16,185,129,.25)}
        .section-note{padding:11px 12px;border-radius:14px;background:linear-gradient(180deg,rgba(37,99,235,.14),rgba(30,64,175,.08));border:1px solid rgba(59,130,246,.16);color:#bfdbfe;font-size:.8rem;line-height:1.5}
        .decor-list{display:grid;gap:10px}
        .decor-row{padding:12px;border-radius:18px;background:linear-gradient(180deg,rgba(25,36,58,.72),rgba(18,28,46,.62));border:1px solid rgba(148,163,184,.12);box-shadow:inset 0 1px 0 rgba(255,255,255,.03)}
        .decor-head{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px}
        .decor-title{display:grid;gap:2px}
        .decor-title small{color:var(--muted);font-size:.74rem}
        .mini-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:10px}
        .mini-grid .field label{font-size:.72rem}
        .preview-panel{display:grid;grid-template-rows:auto 1fr;min-height:0;overflow:hidden}
        .preview-shell{overflow:auto;border-radius:18px;border:1px solid var(--line);background:rgba(8,15,29,.9);padding:12px;min-height:0;height:100%;display:flex;align-items:flex-start;justify-content:center}
        .preview-stage{position:relative;flex:0 0 auto;margin:auto;transform-origin:top left}
        .parking-canvas{position:absolute;left:0;top:0;background:linear-gradient(rgba(86,110,145,.12) 1px,transparent 1px),linear-gradient(90deg,rgba(86,110,145,.12) 1px,transparent 1px),linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01));background-size:32px 32px,32px 32px,100% 100%;border:2px solid rgba(226,232,240,.35);border-radius:24px;transform-origin:top left}.parking-canvas.no-grid{background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01))}.parking-canvas::before{content:"";position:absolute;inset:18px;border:1px dashed rgba(226,232,240,.22);border-radius:18px;pointer-events:none}
        .layout-item{position:absolute;display:grid;place-items:center;color:#d7e3f8;text-align:center;transform-origin:center center}.layout-item span{font-size:.72rem;letter-spacing:.04em;text-transform:uppercase}.layout-lane{border:1px dashed rgba(226,232,240,.28);background:rgba(255,255,255,.02);border-radius:18px}.layout-island{border:2px solid rgba(226,232,240,.36);border-radius:26px;background:rgba(148,163,184,.06)}.layout-building{border:1px solid rgba(148,163,184,.34);background:rgba(30,41,59,.74);border-radius:10px}.layout-entry{border:1px dashed rgba(96,165,250,.38);border-radius:14px;background:rgba(59,130,246,.06)}.layout-label{background:transparent}
        .preview-note{display:none}
        .preview-tools{display:flex;gap:8px;flex-wrap:wrap}
        .preview-chip{display:inline-flex;align-items:center;gap:8px;padding:7px 11px;border-radius:999px;background:rgba(8,15,29,.56);border:1px solid rgba(148,163,184,.12);color:#d9e5f6;font-size:.74rem}
        .preview-spot{position:absolute;border-radius:14px;border:1px solid rgba(148,163,184,.28);background:rgba(15,23,42,.94);display:grid;place-items:center;text-align:center;padding:6px;cursor:grab;user-select:none;transform-origin:center center}.preview-spot strong{display:block;font-size:.95rem}.preview-spot span{font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}.preview-spot.active{outline:2px solid #60a5fa;box-shadow:0 0 0 3px rgba(59,130,246,.18)}
        .decor-panel{min-height:0}
        .spots-panel{display:grid;grid-template-rows:auto auto 1fr;min-height:0;height:100%;overflow:hidden}
        .spot-card-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;min-height:0;overflow:auto;padding-right:6px;align-content:start}
        .spots-sidebar .spot-card-grid{grid-template-columns:1fr}
        .spot-card{padding:12px;border-radius:16px;background:var(--panel-soft);border:1px solid rgba(148,163,184,.12);display:grid;gap:10px}
        .spot-card.active{border-color:rgba(96,165,250,.6);box-shadow:0 0 0 2px rgba(59,130,246,.16)}
        .spot-card-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
        .spot-card-head strong{display:block;font-size:1rem}
        .spot-card-head span{display:block;color:var(--muted);font-size:.74rem;text-transform:uppercase;letter-spacing:.08em}
        .code-pill{display:inline-flex;padding:6px 10px;border-radius:999px;background:rgba(59,130,246,.14);color:#bfdbfe;font-weight:700}
        .spot-field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
        .spot-field-grid .field label{font-size:.7rem}
        .spot-help{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px}
        .spot-help strong{color:#dbe7f7}
        .inspector-stack::-webkit-scrollbar,.preview-shell::-webkit-scrollbar,.spot-card-grid::-webkit-scrollbar{width:10px;height:10px}
        .inspector-stack::-webkit-scrollbar-thumb,.preview-shell::-webkit-scrollbar-thumb,.spot-card-grid::-webkit-scrollbar-thumb{background:rgba(148,163,184,.45);border-radius:999px;border:2px solid transparent;background-clip:padding-box}
        .inspector-stack::-webkit-scrollbar-thumb:hover,.preview-shell::-webkit-scrollbar-thumb:hover,.spot-card-grid::-webkit-scrollbar-thumb:hover{background:rgba(148,163,184,.65)}
        .inspector-stack::-webkit-scrollbar-track,.preview-shell::-webkit-scrollbar-track,.spot-card-grid::-webkit-scrollbar-track{background:transparent}
        @media (max-width:1500px){.layout-grid{grid-template-columns:320px minmax(0,1fr) 330px}}
        @media (max-width:1220px){body{overflow:auto}.page{height:auto;display:block;padding:16px 12px 26px;overflow:visible}#layout-form{min-height:unset;height:auto}.layout-grid{grid-template-columns:1fr;height:auto;min-height:auto}.inspector-stack,.spots-sidebar{max-height:none;overflow:visible;padding-right:0}.workspace-stack{height:auto;min-height:auto;overflow:visible}.preview-panel,.spots-panel{overflow:visible}.spots-panel{height:auto;grid-template-rows:auto auto auto}.preview-shell{min-height:420px}}
        @media (max-width:900px){.overview-stats,.meta-strip,.spot-card-grid{grid-template-columns:1fr}.mini-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media (max-width:720px){.form-row{grid-template-columns:1fr}.hero-actions{width:100%}.hero-actions .btn{flex:1}.mini-grid,.spot-field-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="page">
        <section class="hero">
            <div>
                <div class="editor-kicker">Workspace del plano</div>
                <h1>Editor de Plano</h1>
                <p>Acomoda cajones, accesos, islas y etiquetas para dibujar tu parque vehicular con un layout propio.</p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al tablero</a>
                <button type="submit" form="layout-form" class="btn btn-primary">Guardar plano</button>
            </div>
        </section>

        <form id="layout-form" method="POST" action="{{ route('layout.update') }}">
            @csrf
            @method('PATCH')

            <section class="layout-grid">
                <div class="stack inspector-stack">
                    <section class="panel inspector-overview">
                        <span class="overview-kicker">Panel lateral</span>
                        <div class="overview-title">Control del editor</div>
                        <p class="overview-copy">Ajusta el lienzo, agrega elementos y mantén el plano visible mientras ordenas tu parque vehicular.</p>
                        <div class="overview-stats">
                            <div class="overview-stat">
                                <strong>{{ $spots->count() }}</strong>
                                <span>Cajones</span>
                            </div>
                            <div class="overview-stat">
                                <strong>{{ count(old('decorations', $layout->decorations ?? [])) }}</strong>
                                <span>Elementos</span>
                            </div>
                            <div class="overview-stat">
                                <strong>{{ $layout->canvas_width }}×{{ $layout->canvas_height }}</strong>
                                <span>Lienzo</span>
                            </div>
                        </div>
                    </section>

                    <section class="panel inspector-panel">
                        <div class="panel-head">
                            <div class="panel-head-copy">
                                <span class="panel-label">Base del plano</span>
                                <h2>Configuracion general</h2>
                                <p>Define el lienzo base y controla la cuadricula del plano.</p>
                            </div>
                        </div>
                        @if (session('status'))
                            <div class="alert" style="margin-bottom:12px">{{ session('status') }}</div>
                        @endif
                        <div class="section-note">Los cambios del lienzo se reflejan al instante en la vista previa de la derecha.</div>
                        <div class="form-grid" style="margin-top:12px">
                            <div class="field">
                                <label>Nombre del plano</label>
                                <input type="text" name="name" value="{{ old('name', $layout->name) }}" required>
                            </div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Ancho del lienzo</label>
                                    <input type="number" min="700" max="1800" name="canvas_width" value="{{ old('canvas_width', $layout->canvas_width) }}" data-canvas-width required>
                                </div>
                                <div class="field">
                                    <label>Alto del lienzo</label>
                                    <input type="number" min="500" max="1400" name="canvas_height" value="{{ old('canvas_height', $layout->canvas_height) }}" data-canvas-height required>
                                </div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" name="show_grid" value="1" data-canvas-grid @checked(old('show_grid', $layout->show_grid))>
                                <span>Mostrar reticula tipo blueprint</span>
                            </label>
                            <div class="meta-strip">
                                <span class="meta-pill"><strong>{{ $spots->count() }}</strong> cajones</span>
                                <span class="meta-pill"><strong>{{ count(old('decorations', $layout->decorations ?? [])) }}</strong> elementos</span>
                            </div>
                        </div>
                    </section>

                    <section class="panel decor-panel inspector-panel">
                        <div class="decor-head">
                            <div class="panel-head-copy">
                                <span class="panel-label">Objetos del croquis</span>
                                <h2>Elementos del plano</h2>
                                <p>Agrega accesos, etiquetas, islas y volumenes sin perder de vista el croquis.</p>
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-decoration">Agregar</button>
                        </div>
                        <div class="decor-list" id="decorations-list">
                            @foreach (old('decorations', $layout->decorations ?? []) as $index => $decoration)
                                <div class="decor-row" data-decoration-row>
                                    <div class="decor-head">
                                        <div class="decor-title">
                                            <strong>Elemento {{ $index + 1 }}</strong>
                                            <small>{{ ucfirst($decoration['type'] ?? 'label') }}</small>
                                        </div>
                                        <button type="button" class="btn btn-danger" data-remove-decoration>Quitar</button>
                                    </div>
                                    <div class="form-row">
                                        <div class="field">
                                            <label>Tipo</label>
                                            <select name="decorations[{{ $index }}][type]" data-decoration-input data-decoration-field="type">
                                                @foreach (['lane' => 'Bahia', 'island' => 'Isla', 'building' => 'Edificio', 'entry' => 'Acceso', 'label' => 'Etiqueta'] as $type => $label)
                                                    <option value="{{ $type }}" @selected(($decoration['type'] ?? '') === $type)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="field">
                                            <label>Texto</label>
                                            <input type="text" name="decorations[{{ $index }}][label]" value="{{ $decoration['label'] ?? '' }}" data-decoration-input data-decoration-field="label">
                                        </div>
                                    </div>
                                    <div class="mini-grid">
                                        <div class="field"><label>X</label><input type="number" name="decorations[{{ $index }}][x]" value="{{ $decoration['x'] ?? 0 }}" data-decoration-input data-decoration-field="x"></div>
                                        <div class="field"><label>Y</label><input type="number" name="decorations[{{ $index }}][y]" value="{{ $decoration['y'] ?? 0 }}" data-decoration-input data-decoration-field="y"></div>
                                        <div class="field"><label>Ancho</label><input type="number" name="decorations[{{ $index }}][width]" value="{{ $decoration['width'] ?? 120 }}" data-decoration-input data-decoration-field="width"></div>
                                        <div class="field"><label>Alto</label><input type="number" name="decorations[{{ $index }}][height]" value="{{ $decoration['height'] ?? 50 }}" data-decoration-input data-decoration-field="height"></div>
                                    </div>
                                    <div class="field" style="margin-top:10px"><label>Rotacion</label><input type="number" min="-180" max="180" name="decorations[{{ $index }}][rotation]" value="{{ $decoration['rotation'] ?? 0 }}" data-decoration-input data-decoration-field="rotation"></div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <div class="stack workspace-stack">
                    <section class="panel preview-panel">
                        <div class="panel-head">
                            <div class="panel-head-copy">
                                <h2>Vista previa interactiva</h2>
                            </div>
                        </div>
                        <div class="preview-shell" data-preview-shell>
                            <div class="preview-stage" data-preview-stage>
                                <div class="parking-canvas {{ $layout->show_grid ? '' : 'no-grid' }}" data-layout-preview style="width: {{ $layout->canvas_width }}px; height: {{ $layout->canvas_height }}px;">
                                <div data-decorations-preview></div>
                                @foreach ($spots as $spot)
                                    <button type="button" class="preview-spot" data-spot-preview="{{ $spot->id }}" style="left: {{ old('spots.'.$loop->index.'.layout_x', $spot->layout_x) }}px; top: {{ old('spots.'.$loop->index.'.layout_y', $spot->layout_y) }}px; width: {{ old('spots.'.$loop->index.'.layout_width', $spot->layout_width) }}px; height: {{ old('spots.'.$loop->index.'.layout_height', $spot->layout_height) }}px; transform: rotate({{ old('spots.'.$loop->index.'.layout_angle', $spot->layout_angle) }}deg);">
                                        <strong>{{ $spot->code }}</strong>
                                        <span>{{ $spot->zone ?: 'Sin zona' }}</span>
                                    </button>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                <div class="stack spots-sidebar">
                    <section class="panel spots-panel">
                        <div class="panel-head">
                            <div class="panel-head-copy">
                                <h2>Cajones</h2>
                                <p>Edicion compacta. Selecciona un cajon en el plano y ajusta sus valores aqui.</p>
                            </div>
                        </div>
                        <div class="spot-help">
                            <span>Tip: al hacer clic en un cajon del plano se resalta su tarjeta correspondiente.</span>
                            <strong>{{ $spots->count() }} cajones cargados</strong>
                        </div>
                        <div class="spot-card-grid">
                            @foreach ($spots as $spot)
                                <article class="spot-card" data-spot-row="{{ $spot->id }}">
                                    <div class="spot-card-head">
                                        <div>
                                            <strong>{{ $spot->code }}</strong>
                                            <span>{{ $spot->zone ?: 'Sin zona' }}</span>
                                        </div>
                                        <span class="code-pill">ID {{ $spot->id }}</span>
                                    </div>
                                    <input type="hidden" name="spots[{{ $loop->index }}][id]" value="{{ $spot->id }}">
                                    <div class="spot-field-grid">
                                        <div class="field">
                                            <label>X</label>
                                            <input type="number" name="spots[{{ $loop->index }}][layout_x]" value="{{ old('spots.'.$loop->index.'.layout_x', $spot->layout_x) }}" data-spot-input="layout_x" data-spot-id="{{ $spot->id }}">
                                        </div>
                                        <div class="field">
                                            <label>Y</label>
                                            <input type="number" name="spots[{{ $loop->index }}][layout_y]" value="{{ old('spots.'.$loop->index.'.layout_y', $spot->layout_y) }}" data-spot-input="layout_y" data-spot-id="{{ $spot->id }}">
                                        </div>
                                        <div class="field">
                                            <label>Ancho</label>
                                            <input type="number" min="50" max="220" name="spots[{{ $loop->index }}][layout_width]" value="{{ old('spots.'.$loop->index.'.layout_width', $spot->layout_width) }}" data-spot-input="layout_width" data-spot-id="{{ $spot->id }}">
                                        </div>
                                        <div class="field">
                                            <label>Alto</label>
                                            <input type="number" min="80" max="280" name="spots[{{ $loop->index }}][layout_height]" value="{{ old('spots.'.$loop->index.'.layout_height', $spot->layout_height) }}" data-spot-input="layout_height" data-spot-id="{{ $spot->id }}">
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label>Rotacion</label>
                                        <input type="number" min="-180" max="180" name="spots[{{ $loop->index }}][layout_angle]" value="{{ old('spots.'.$loop->index.'.layout_angle', $spot->layout_angle) }}" data-spot-input="layout_angle" data-spot-id="{{ $spot->id }}">
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>
            </section>
        </form>
    </div>

    <template id="decoration-template">
        <div class="decor-row" data-decoration-row>
            <div class="decor-head">
                <div class="decor-title">
                    <strong>Elemento</strong>
                    <small>Nuevo elemento</small>
                </div>
                <button type="button" class="btn btn-danger" data-remove-decoration>Quitar</button>
            </div>
            <div class="form-row">
                <div class="field"><label>Tipo</label><select data-decoration-input data-decoration-field="type"><option value="lane">Bahia</option><option value="island">Isla</option><option value="building">Edificio</option><option value="entry">Acceso</option><option value="label">Etiqueta</option></select></div>
                <div class="field"><label>Texto</label><input type="text" data-decoration-input data-decoration-field="label"></div>
            </div>
            <div class="mini-grid">
                <div class="field"><label>X</label><input type="number" value="0" data-decoration-input data-decoration-field="x"></div>
                <div class="field"><label>Y</label><input type="number" value="0" data-decoration-input data-decoration-field="y"></div>
                <div class="field"><label>Ancho</label><input type="number" value="120" data-decoration-input data-decoration-field="width"></div>
                <div class="field"><label>Alto</label><input type="number" value="50" data-decoration-input data-decoration-field="height"></div>
            </div>
            <div class="field" style="margin-top:10px"><label>Rotacion</label><input type="number" value="0" data-decoration-input data-decoration-field="rotation"></div>
        </div>
    </template>

    <script>
        const canvas = document.querySelector('[data-layout-preview]');
        const previewShell = document.querySelector('[data-preview-shell]');
        const previewStage = document.querySelector('[data-preview-stage]');
        const canvasWidthInput = document.querySelector('[data-canvas-width]');
        const canvasHeightInput = document.querySelector('[data-canvas-height]');
        const canvasGridInput = document.querySelector('[data-canvas-grid]');
        const decorationsList = document.getElementById('decorations-list');
        const decorationsPreview = document.querySelector('[data-decorations-preview]');
        const decorationTemplate = document.getElementById('decoration-template');
        const selectedSpotLabel = document.querySelector('[data-selected-spot-label]');
        let activeDrag = null;
        let previewScale = 1;

        function fitPreviewToViewport() {
            if (!canvas || !previewShell || !previewStage) return;

            const width = Number(canvasWidthInput.value || 700);
            const height = Number(canvasHeightInput.value || 500);
            const availableWidth = Math.max(previewShell.clientWidth - 4, 0);
            const availableHeight = Math.max(previewShell.clientHeight - 4, 0);
            const scale = Math.min(availableWidth / width, availableHeight / height);

            previewScale = Math.min(1, Math.max(0.35, scale || 1));
            previewStage.style.width = `${Math.round(width * previewScale)}px`;
            previewStage.style.height = `${Math.round(height * previewScale)}px`;
            canvas.style.transform = `scale(${previewScale})`;
        }

        function updateCanvasFrame() {
            canvas.style.width = `${canvasWidthInput.value || 700}px`;
            canvas.style.height = `${canvasHeightInput.value || 500}px`;
            canvas.classList.toggle('no-grid', !canvasGridInput.checked);
            fitPreviewToViewport();
        }

        function syncSpotCard(spotId) {
            const card = document.querySelector(`[data-spot-preview="${spotId}"]`);
            const values = {};
            document.querySelectorAll(`[data-spot-id="${spotId}"]`).forEach((input) => values[input.dataset.spotInput] = Number(input.value || 0));
            if (!card) return;
            card.style.left = `${values.layout_x || 0}px`;
            card.style.top = `${values.layout_y || 0}px`;
            card.style.width = `${values.layout_width || 80}px`;
            card.style.height = `${values.layout_height || 140}px`;
            card.style.transform = `rotate(${values.layout_angle || 0}deg)`;
        }

        function renderDecorations() {
            decorationsPreview.innerHTML = '';
            decorationsList.querySelectorAll('[data-decoration-row]').forEach((row) => {
                const values = {};
                row.querySelectorAll('[data-decoration-input]').forEach((input) => values[input.dataset.decorationField] = input.value);
                if (!values.type) return;
                const item = document.createElement('div');
                item.className = `layout-item layout-${values.type}`;
                item.style.left = `${Number(values.x || 0)}px`;
                item.style.top = `${Number(values.y || 0)}px`;
                item.style.width = `${Number(values.width || 100)}px`;
                item.style.height = `${Number(values.height || 40)}px`;
                item.style.transform = `rotate(${Number(values.rotation || 0)}deg)`;
                if (values.label) {
                    const label = document.createElement('span');
                    label.textContent = values.label;
                    item.appendChild(label);
                }
                decorationsPreview.appendChild(item);
            });
        }

        function reindexDecorations() {
            decorationsList.querySelectorAll('[data-decoration-row]').forEach((row, index) => {
                row.querySelector('.decor-head strong').textContent = `Elemento ${index + 1}`;
                row.querySelectorAll('[data-decoration-input]').forEach((input) => input.name = `decorations[${index}][${input.dataset.decorationField}]`);
            });
        }

        function setActiveSpot(spotId) {
            document.querySelectorAll('[data-spot-preview]').forEach((item) => {
                item.classList.toggle('active', item.dataset.spotPreview === String(spotId));
            });

            document.querySelectorAll('[data-spot-row]').forEach((row) => {
                row.classList.toggle('active', row.dataset.spotRow === String(spotId));
            });

            const activeCard = document.querySelector(`[data-spot-row="${spotId}"]`);
            const activeSpot = document.querySelector(`[data-spot-preview="${spotId}"] strong`);

            if (selectedSpotLabel) {
                selectedSpotLabel.textContent = activeSpot ? activeSpot.textContent : 'Ninguno';
            }

            activeCard?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }

        function wireDecorationRow(row) {
            row.querySelectorAll('[data-decoration-input]').forEach((input) => {
                input.addEventListener('input', renderDecorations);
                input.addEventListener('change', renderDecorations);
            });
            row.querySelector('[data-remove-decoration]')?.addEventListener('click', () => {
                row.remove();
                reindexDecorations();
                renderDecorations();
            });
        }

        document.getElementById('add-decoration').addEventListener('click', () => {
            decorationsList.appendChild(decorationTemplate.content.cloneNode(true));
            wireDecorationRow(decorationsList.lastElementChild);
            reindexDecorations();
            renderDecorations();
        });

        decorationsList.querySelectorAll('[data-decoration-row]').forEach(wireDecorationRow);

        document.querySelectorAll('[data-spot-input]').forEach((input) => {
            input.addEventListener('input', () => syncSpotCard(input.dataset.spotId));
            input.addEventListener('focus', () => setActiveSpot(input.dataset.spotId));
        });

        document.querySelectorAll('[data-spot-preview]').forEach((card) => {
            card.addEventListener('pointerdown', (event) => {
                const spotId = card.dataset.spotPreview;
                const xInput = document.querySelector(`[data-spot-id="${spotId}"][data-spot-input="layout_x"]`);
                const yInput = document.querySelector(`[data-spot-id="${spotId}"][data-spot-input="layout_y"]`);
                activeDrag = {spotId, startX: event.clientX, startY: event.clientY, baseX: Number(xInput.value || 0), baseY: Number(yInput.value || 0)};
                setActiveSpot(spotId);
            });

            card.addEventListener('click', () => setActiveSpot(card.dataset.spotPreview));
        });

        window.addEventListener('pointermove', (event) => {
            if (!activeDrag) return;
            const xInput = document.querySelector(`[data-spot-id="${activeDrag.spotId}"][data-spot-input="layout_x"]`);
            const yInput = document.querySelector(`[data-spot-id="${activeDrag.spotId}"][data-spot-input="layout_y"]`);
            xInput.value = Math.max(0, activeDrag.baseX + Math.round((event.clientX - activeDrag.startX) / previewScale));
            yInput.value = Math.max(0, activeDrag.baseY + Math.round((event.clientY - activeDrag.startY) / previewScale));
            syncSpotCard(activeDrag.spotId);
        });

        window.addEventListener('pointerup', () => activeDrag = null);
        canvasWidthInput.addEventListener('input', updateCanvasFrame);
        canvasHeightInput.addEventListener('input', updateCanvasFrame);
        canvasGridInput.addEventListener('change', updateCanvasFrame);
        window.addEventListener('resize', fitPreviewToViewport);

        document.querySelectorAll('[data-spot-preview]').forEach((card) => syncSpotCard(card.dataset.spotPreview));
        if (document.querySelector('[data-spot-preview]')) {
            setActiveSpot(document.querySelector('[data-spot-preview]').dataset.spotPreview);
        }
        updateCanvasFrame();
        renderDecorations();
    </script>
</body>
</html>
