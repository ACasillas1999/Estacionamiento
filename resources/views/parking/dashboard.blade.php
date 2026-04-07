@php
    $selectedSpot = old('parking_spot_id') ? $spots->firstWhere('id', (int) old('parking_spot_id')) : null;
    $shouldOpenCheckInModal = $errors->checkIn->any() || old('parking_spot_id');
    $shouldOpenSpotModal = $errors->spot->any() || old('code') || old('zone');
    $decorations = $layout->decorations ?? [];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estacionamiento A</title>
    <style>
        :root{--bg:#07111f;--panel:rgba(12,20,36,.84);--panel-soft:rgba(28,39,60,.54);--border:rgba(148,163,184,.14);--text:#f8fafc;--muted:#9fb0c8;--blue:#3b82f6;--green:#18c08f;--amber:#f7a90a;--red:#ef4444;--radius:20px;--shadow:0 24px 60px rgba(0,0,0,.34);--side:#314563;--side-dark:#253751;--side-line:rgba(255,255,255,.08);--left-sidebar-width:248px;--right-sidebar-width:286px;--sidebar-collapsed-width:74px}
        *{box-sizing:border-box} body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:radial-gradient(circle at 12% 0%,rgba(59,130,246,.2),transparent 32%),radial-gradient(circle at 100% 100%,rgba(16,185,129,.12),transparent 32%),linear-gradient(180deg,#09111f 0%,#08101d 55%,#07101a 100%);color:var(--text);min-height:100vh;line-height:1.5;overflow:hidden}
        body.sidebar-left-collapsed{--left-sidebar-width:var(--sidebar-collapsed-width)}
        body.sidebar-right-collapsed{--right-sidebar-width:var(--sidebar-collapsed-width)}
        .page{position:relative;isolation:isolate;max-width:none;margin:0;padding:0}
        .page::before,.page::after{content:"";position:fixed;inset:auto;pointer-events:none;z-index:-1;border-radius:999px;filter:blur(90px);opacity:.5}
        .page::before{width:320px;height:320px;top:-90px;left:-110px;background:rgba(59,130,246,.18)}
        .page::after{width:360px;height:360px;right:-120px;bottom:-120px;background:rgba(16,185,129,.12)}
        .hero{margin:0}.hero h1{margin:0 0 8px;font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-0.02em}.hero p{margin:0;color:var(--muted);font-size:1.1rem}
        .app-grid{display:grid;grid-template-columns:var(--left-sidebar-width) minmax(0,1fr) var(--right-sidebar-width);gap:10px;align-items:start;height:100vh;transition:grid-template-columns .22s ease,gap .22s ease}
        .sidebar{display:grid;grid-template-rows:auto 1fr;gap:0;position:sticky;top:0;height:100vh;background:linear-gradient(180deg,rgba(55,78,112,.94) 0%,rgba(38,56,84,.96) 100%);border:1px solid var(--side-line);border-left:0;border-top:0;border-bottom:0;border-radius:0;box-shadow:var(--shadow),inset -1px 0 0 rgba(255,255,255,.03);min-width:0;transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);z-index:10}
        .sidebar-left{overflow:visible}
        .sidebar-right{border-left:1px solid var(--side-line);border-right:0;overflow:hidden}
        .sidebar, .side-action, .side-link, .sidebar-brand, .sidebar-kicker, .sidebar-logo, .side-text, .side-icon, .sidebar-search, .stat { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) }
        .sidebar-brand{padding:16px 16px 12px;background:linear-gradient(180deg,rgba(7,16,30,.2),rgba(7,16,30,.08));border-bottom:1px solid var(--side-line)}
        .sidebar-head-row{display:flex;align-items:center;justify-content:space-between;gap:10px}
        .sidebar-kicker{display:flex;align-items:center;gap:10px;font-size:.76rem;text-transform:uppercase;letter-spacing:.12em;color:#dbe7f7;font-weight:800}
        .sidebar-logo{width:22px;height:22px;border-radius:6px;display:grid;place-items:center;background:linear-gradient(180deg,#38bdf8,#2563eb);box-shadow:0 8px 18px rgba(37,99,235,.28),inset 0 0 0 1px rgba(255,255,255,.16)}
        .sidebar-logo svg{width:14px;height:14px;display:block;stroke:#eff6ff;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;fill:none}
        .sidebar-toggle{width:32px;height:32px;border-radius:11px;border:1px solid rgba(255,255,255,.12);background:rgba(15,23,42,.26);color:#dbe7f7;display:grid;place-items:center;padding:0;flex-shrink:0;box-shadow:inset 0 1px 0 rgba(255,255,255,.06)}
        .sidebar-toggle:hover{background:rgba(15,23,42,.38);box-shadow:0 8px 18px rgba(0,0,0,.14)}
        .toggle-icon{width:16px;height:16px;display:block}
        .toggle-icon polyline{fill:none;stroke:currentColor;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
        .sidebar-brand h2{margin:12px 0 6px;font-size:1.34rem;line-height:1.05;letter-spacing:-.03em}
        .sidebar-brand p{margin:0;color:#c4d3e8;font-size:.86rem;line-height:1.55}
        .sidebar-search{padding:12px 14px;border-bottom:1px solid var(--side-line)}
        .search-shell{position:relative}
        .search-shell input{width:100%;padding:11px 40px 11px 14px;border-radius:14px;border:1px solid rgba(255,255,255,.08);background:rgba(8,15,29,.24);color:var(--text);font:inherit;box-shadow:inset 0 1px 0 rgba(255,255,255,.04)}
        .search-shell input::placeholder{color:#c4d3e8}
        .search-shell span{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#dbe7f7;font-weight:700;opacity:.84}
        .sidebar-card{padding:0;border-top:1px solid var(--side-line)}
        .sidebar-actions{display:grid;grid-template-columns:1fr;gap:0}
        .side-action,.side-link{width:100%;border:0;background:transparent;color:var(--text);text-decoration:none;padding:14px 14px;display:grid;grid-template-columns:22px 1fr auto;align-items:center;gap:10px;text-align:left;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.05);position:relative;transition:background .18s ease,transform .18s ease}
        .side-action:hover,.side-link:hover{background:rgba(7,16,30,.18)}
        .side-action.is-active{background:linear-gradient(90deg,rgba(14,165,233,.18),rgba(14,165,233,.04))}
        .side-action.is-active::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#38bdf8,#2563eb);box-shadow:0 0 14px rgba(56,189,248,.42)}
        .side-icon{display:grid;place-items:center;width:24px;height:24px;color:#dbe7f7;font-weight:700;text-shadow:0 0 12px rgba(255,255,255,.14)}
        .side-text strong{display:block;font-size:.95rem;font-weight:600}
        .side-text span{display:block;margin-top:2px;color:#c4d3e8;font-size:.76rem}
        .side-pill{padding:2px 8px;border-radius:999px;background:linear-gradient(180deg,#86efac,#34d399);color:#0f172a;font-size:.7rem;font-weight:800;box-shadow:0 8px 16px rgba(52,211,153,.22)}
        .side-arrow{color:#dbe7f7;font-size:1rem;opacity:.7}
        .stats{display:grid;gap:0}
        .stats-title{padding:12px 14px 10px;color:#c4d3e8;text-transform:uppercase;letter-spacing:.12em;font-size:.68rem;border-bottom:1px solid rgba(255,255,255,.05)}
        .stat{background:transparent;border:0;border-radius:0;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid rgba(255,255,255,.05);transition:background .18s ease}
        .stat:last-child{border-bottom:0}
        .stat:hover{background:rgba(8,15,29,.12)}
        .stat-icon{width:38px;height:38px;border-radius:12px;display:grid;place-items:center;flex-shrink:0;box-shadow:inset 0 1px 0 rgba(255,255,255,.05)}
        .stat-body{display:flex;align-items:center;gap:12px}
        .stat-info small{display:block;color:#c4d3e8;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:700}
        .stat-info strong{display:block;font-size:0.92rem;font-weight:600}
        .stat-value{font-size:1.35rem;font-weight:800}
        .stat-total .stat-icon{background:rgba(59,130,246,.15);color:var(--blue)}
        .stat-available .stat-icon{background:rgba(16,185,129,.15);color:var(--green)}
        .stat-occupied .stat-icon{background:rgba(245,158,11,.15);color:var(--amber)}
        .stat-inactive .stat-icon{background:rgba(239,68,68,.15);color:var(--red)}

        .main-shell{padding:10px 0;display:grid;gap:10px;height:100vh;overflow-y:auto;overflow-x:hidden}
        .main-shell::-webkit-scrollbar{width:10px}
        .main-shell::-webkit-scrollbar-thumb{background:rgba(148,163,184,.25);border-radius:999px}
        .panel,.modal-card{background:linear-gradient(180deg,rgba(14,23,40,.88),rgba(10,18,32,.82));backdrop-filter:blur(12px);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow),inset 0 1px 0 rgba(255,255,255,.04);padding:24px}
        .panel h2{margin:0 0 16px;font-size:1.25rem;font-weight:700;display:flex;align-items:center;gap:10px}
        .panel p{margin:0 0 20px;color:var(--muted);font-size:0.95rem}
        .content-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:10px;align-items:start;min-height:100%;height:100%}
        .tips,.vehicle-list{display:grid;gap:12px}.tip,.vehicle{padding:14px;border-radius:14px;background:var(--panel-soft);border:1px solid var(--border)}.tip strong{display:block;color:var(--text);margin-bottom:4px}
        .alert{padding:14px 16px;border-radius:14px;margin-bottom:18px}.alert.success{background:rgba(16,185,129,.14);color:#6ee7b7;border:1px solid rgba(16,185,129,.25)}.alert.error{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.24)}.alert.error ul{margin:8px 0 0 18px;padding:0}
        .toast-stack{position:fixed;top:18px;right:18px;z-index:80;display:grid;gap:12px;pointer-events:none}
        .toast{min-width:min(360px,calc(100vw - 32px));max-width:420px;padding:14px 16px;border-radius:16px;border:1px solid rgba(16,185,129,.28);background:rgba(5,18,34,.94);box-shadow:0 18px 50px rgba(0,0,0,.35);display:flex;align-items:flex-start;justify-content:space-between;gap:14px;pointer-events:auto}
        .toast-success{box-shadow:0 18px 50px rgba(0,0,0,.35),0 0 0 1px rgba(16,185,129,.12)}
        .toast-copy{display:grid;gap:3px}
        .toast-copy strong{font-size:.86rem;letter-spacing:.04em;text-transform:uppercase;color:#6ee7b7}
        .toast-copy span{color:#e2e8f0}
        .toast-close{width:34px;height:34px;border-radius:10px;padding:0;flex-shrink:0}
        .form-grid{display:grid;gap:14px}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{display:grid;gap:6px}.field label{font-size:.85rem;color:var(--muted)} input,select,textarea,button{font:inherit} input,select,textarea{width:100%;padding:12px 13px;border-radius:12px;border:1px solid var(--border);background:#0f172a;color:var(--text)} textarea{min-height:86px;resize:vertical}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:12px 14px;cursor:pointer;transition:.18s ease;font-weight:600;text-decoration:none;text-align:center}.btn:hover{transform:translateY(-1px)}.btn-primary{background:linear-gradient(180deg,#4f8dff,#2563eb);color:white;box-shadow:0 14px 24px rgba(37,99,235,.24)}.btn-secondary{background:rgba(148,163,184,.14);color:var(--text)}.btn-success{background:linear-gradient(180deg,#1fcd97,#10b981);color:white;box-shadow:0 12px 20px rgba(16,185,129,.22)}.btn-ghost{background:transparent;color:var(--muted);border:1px solid var(--border)}
        .help-btn{width:44px;height:44px;padding:0;border-radius:999px;font-size:1.2rem;line-height:1;background:rgba(148,163,184,.14);color:var(--text)}
        .plan-panel{display:grid;grid-template-rows:auto 1fr;height:calc(100vh - 20px);min-height:calc(100vh - 20px);padding:10px;overflow:hidden;position:relative}
        .plan-panel::before{content:"";position:absolute;inset:0 0 auto 0;height:120px;background:linear-gradient(180deg,rgba(37,99,235,.08),transparent);pointer-events:none}
        .plan-panel .panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:2px 2px 12px}
        .plan-panel .panel-head h2{margin:0 0 6px}
        .plan-panel .panel-head p{margin:0;max-width:720px}
        .plan-panel .panel-head,.plan-shell{position:relative;z-index:1}
        .panel-kicker{display:inline-flex;align-items:center;gap:8px;margin-bottom:10px;padding:6px 10px;border-radius:999px;background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.18);color:#a5c8ff;font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;font-weight:800}
        .plan-meta{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px}
        .plan-chip{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:rgba(8,15,29,.56);border:1px solid rgba(148,163,184,.12);color:#d9e5f6;font-size:.76rem}
        .plan-chip strong{font-size:.78rem}
        .plan-toolbar{display:flex;align-items:center;justify-content:flex-end;gap:8px;margin-bottom:8px}
        .plan-toolbar-copy{display:none}
        .plan-tools{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
        .plan-tool-btn{min-width:40px;padding:8px 11px;border-radius:12px;border:1px solid rgba(148,163,184,.14);background:rgba(8,15,29,.5);color:#dbe7f7;font-weight:700;cursor:pointer;transition:.18s ease}
        .plan-tool-btn:hover{background:rgba(15,23,42,.72)}
        .plan-tool-btn.is-active{background:rgba(59,130,246,.16);border-color:rgba(59,130,246,.3);color:#bfdbfe}
        .plan-tool-btn svg{width:16px;height:16px;display:block}
        .plan-tool-btn svg *{stroke:currentColor}
        .plan-scale-pill{display:inline-flex;align-items:center;justify-content:center;min-width:54px;padding:8px 10px;border-radius:999px;background:rgba(8,15,29,.58);border:1px solid rgba(148,163,184,.14);font-size:.72rem;font-weight:800;color:#e2e8f0}
        .plan-shell{overflow:auto;border-radius:20px;border:1px solid rgba(148,163,184,.14);background:linear-gradient(180deg,rgba(7,15,29,.96),rgba(7,14,25,.92));padding:10px;min-height:0;height:100%;display:flex;align-items:flex-start;justify-content:flex-start;box-shadow:inset 0 1px 0 rgba(255,255,255,.04)}
        .plan-shell::-webkit-scrollbar{height:10px;width:10px}
        .plan-shell::-webkit-scrollbar-thumb{background:rgba(148,163,184,.22);border-radius:999px}
        .plan-shell.is-pan-mode{cursor:grab;touch-action:none}
        .plan-shell.is-pan-mode .parking-canvas{pointer-events:none}
        .plan-shell.is-panning{cursor:grabbing;user-select:none}
        .plan-stage{position:relative;flex:0 0 auto;margin:0 auto;display:block}
        .parking-canvas{position:absolute;left:0;top:0;overflow:hidden;background:radial-gradient(circle at 50% 0%,rgba(59,130,246,.07),transparent 35%),linear-gradient(rgba(95,120,154,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(95,120,154,.1) 1px,transparent 1px),linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.01));background-size:100% 100%,32px 32px,32px 32px,100% 100%;border:2px solid rgba(226,232,240,.28);border-radius:28px;box-shadow:inset 0 0 0 1px rgba(255,255,255,.04),0 20px 40px rgba(0,0,0,.2);transform-origin:top left;transition:transform .18s ease}
        .parking-canvas.no-grid{background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01))}
        .parking-canvas::before{content:"";position:absolute;inset:18px;border:1px dashed rgba(226,232,240,.18);border-radius:20px;pointer-events:none}
        .parking-canvas::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.03),transparent 22%,transparent 80%,rgba(0,0,0,.12));pointer-events:none}
        .layout-item{position:absolute;display:grid;color:#d7e3f8;text-align:center;transform-origin:center center;pointer-events:none;z-index:1;padding:10px}
        .layout-item span{display:inline-flex;align-items:center;justify-content:center;max-width:100%;padding:5px 11px;border-radius:999px;background:rgba(9,15,28,.84);border:1px solid rgba(148,163,184,.12);font-size:.68rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;line-height:1.2;text-shadow:0 1px 0 rgba(0,0,0,.45);backdrop-filter:blur(4px);box-shadow:0 10px 18px rgba(0,0,0,.14)}
        .layout-lane{border:1px dashed rgba(226,232,240,.22);background:rgba(255,255,255,.015);border-radius:18px}
        .layout-island{border:2px solid rgba(226,232,240,.3);border-radius:26px;background:rgba(148,163,184,.045);box-shadow:inset 0 0 0 1px rgba(255,255,255,.02)}
        .layout-building{border:1px solid rgba(148,163,184,.24);background:linear-gradient(180deg,rgba(34,46,68,.78),rgba(28,40,61,.74));border-radius:14px}
        .layout-entry{border:1px dashed rgba(96,165,250,.42);border-radius:14px;background:rgba(59,130,246,.08)}
        .layout-entry::after{content:"";width:42px;height:2px;background:#93c5fd;box-shadow:24px 0 0 #93c5fd;position:absolute}
        .layout-entry::before{content:"";position:absolute;right:34px;border-top:7px solid transparent;border-bottom:7px solid transparent;border-left:12px solid #93c5fd}
        .layout-label{background:transparent;border:0}
        .layout-lane span,.layout-island span{align-self:start;justify-self:start;margin:10px 0 0 10px}
        .layout-building span{align-self:center;justify-self:center;max-width:140px}
        .layout-entry span{align-self:center;justify-self:start;margin-left:14px}
        .layout-label span{background:rgba(8,15,29,.62)}
        .compass{position:absolute;top:26px;right:30px;width:76px;height:76px;border:1px solid rgba(226,232,240,.2);border-radius:999px;display:grid;place-items:center;background:rgba(7,12,22,.5);box-shadow:0 14px 22px rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.04)}
        .compass::before,.compass::after{content:"";position:absolute;background:rgba(226,232,240,.4)}
        .compass::before{width:1px;height:52px}
        .compass::after{height:1px;width:52px}
        .compass-mark{position:absolute;top:8px;font-size:.82rem;letter-spacing:.2em}
        .compass-arrow{width:0;height:0;border-left:10px solid transparent;border-right:10px solid transparent;border-bottom:24px solid #f8fafc;transform:translateY(-10px)}
        .plan-spot{position:absolute;z-index:5;overflow:hidden;border-radius:18px;border:1px solid rgba(148,163,184,.12);background:linear-gradient(180deg,rgba(20,31,53,.96),rgba(15,24,42,.98));padding:8px 7px 10px;color:var(--text);text-align:center;display:grid;justify-items:center;align-content:start;gap:4px;transform-origin:center center;box-shadow:0 14px 30px rgba(0,0,0,.22),inset 0 1px 0 rgba(255,255,255,.04);transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
        .plan-spot.free{border-top:3px solid var(--green);cursor:pointer}
        .plan-spot.free:hover{box-shadow:0 18px 28px rgba(14,165,233,.16),0 0 0 1px rgba(56,189,248,.18);border-color:rgba(59,130,246,.26);transform:translateY(-2px) rotate(var(--spot-rotation,0deg))}
        .plan-spot.busy{border-top:3px solid var(--amber);padding:10px 8px 12px;gap:5px;align-content:start}
        .plan-spot.off{border-top:3px solid var(--red);opacity:.72}
        .plan-spot-code{font-size:.92rem;font-weight:800;line-height:1}
        .plan-spot-zone{font-size:.56rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;line-height:1.05}
        .plan-badge{display:inline-flex;justify-content:center;align-items:center;gap:5px;padding:3px 7px;border-radius:999px;font-size:.56rem;text-transform:uppercase;letter-spacing:.05em;margin:0 auto;max-width:100%;font-weight:800}
        .plan-badge::before{content:"";width:7px;height:7px;border-radius:999px;background:currentColor}
        .plan-spot.free .plan-badge{background:rgba(16,185,129,.14);color:var(--green)}
        .plan-spot.busy .plan-badge{background:rgba(245,158,11,.14);color:var(--amber)}
        .plan-spot.off .plan-badge{background:rgba(239,68,68,.14);color:var(--red)}
        .plan-spot-note{font-size:.58rem;line-height:1.15;color:#8fdcff}
        .plan-spot.is-tight{padding:8px 6px 10px;gap:4px}
        .plan-spot.is-tight .plan-spot-code{font-size:.86rem}
        .plan-spot.is-tight .plan-spot-zone{font-size:.52rem;letter-spacing:.03em}
        .plan-spot.is-tight .plan-badge{padding:3px 6px;font-size:.52rem}
        .plan-spot.is-tight .plate-chip{padding:3px 5px;font-size:.62rem}
        .plan-spot.is-tight.busy .spot-live span{padding:3px 4px;font-size:.5rem}
        .plan-spot.is-tight.busy .spot-live strong{font-size:.7rem}
        .plan-spot.is-compact{padding:8px 6px;gap:4px;align-content:center}
        .plan-spot.is-compact .plan-spot-code{font-size:.9rem}
        .plan-spot.is-compact .plan-spot-zone{font-size:.58rem;letter-spacing:.04em}
        .plan-spot.is-compact .plan-badge{padding:3px 6px;font-size:.58rem}
        .plan-spot.is-compact .plan-spot-note,.plan-spot.is-compact .spot-live{display:none}
        .plan-spot.is-compact .plate-chip{padding:4px 6px;font-size:.7rem}
        .plan-spot.busy .plan-spot-code{font-size:.88rem;line-height:1}
        .plan-spot.busy .plan-spot-zone{font-size:.5rem;letter-spacing:.04em;line-height:1.05}
        .plan-spot.busy .plan-badge{padding:3px 6px;font-size:.52rem}
        .plate-chip{margin:0 auto;display:flex;align-items:center;justify-content:center;min-height:24px;padding:5px 8px;border-radius:10px;background:rgba(2,6,23,.68);border:1px dashed rgba(148,163,184,.2);color:#f8fafc;font-family:Consolas,monospace;font-size:.76rem;font-weight:700;letter-spacing:.04em;line-height:1}
        .plan-spot.busy .plate-chip{width:100%;min-height:22px;padding:4px 6px;font-size:.68rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .spot-live{margin-top:3px;display:grid;gap:3px;width:100%;font-size:.66rem;color:#c7d7ec}
        .spot-live strong{color:#fff;font-size:.82rem}
        .plan-spot.busy .spot-live{margin-top:1px;gap:3px}
        .plan-spot.busy .spot-live span{display:grid;gap:1px;padding:3px 5px;border-radius:9px;background:rgba(2,6,23,.34);font-size:.5rem;line-height:1;text-align:center}
        .plan-spot.busy .spot-live strong{display:block;margin-top:0;font-size:.72rem;white-space:nowrap}
        .vehicles-panel{display:grid;grid-template-rows:auto auto 1fr;min-width:0}
        .vehicles-header{padding:10px 12px 8px;background:linear-gradient(180deg,rgba(7,16,30,.2),rgba(7,16,30,.08));border-bottom:1px solid var(--side-line)}
        .vehicles-header h2{margin:0 0 3px;font-size:1rem;line-height:1.05}
        .vehicles-header p{margin:0;color:#c4d3e8;font-size:.68rem;line-height:1.22}
        .vehicles-summary{padding:7px 12px;border-bottom:1px solid var(--side-line)}
        .vehicles-summary-row{display:flex;align-items:center;justify-content:space-between;gap:8px}
        .vehicles-summary strong{font-size:1.15rem;line-height:1}
        .vehicles-summary span{display:block;color:#c4d3e8;font-size:.64rem;text-transform:uppercase;letter-spacing:.08em}
        .vehicles-body{padding:6px;overflow-y:auto}
        .vehicle-list{gap:6px}
        .vehicle{display:grid;grid-template-columns:minmax(0,1fr) 68px;align-items:center;gap:7px;padding:7px 8px;border-radius:12px;background:linear-gradient(180deg,rgba(40,55,84,.6),rgba(32,45,70,.56));border:1px solid rgba(255,255,255,.06);box-shadow:inset 0 1px 0 rgba(255,255,255,.04)}
        .vehicle-main{display:grid;gap:3px;min-width:0}
        .vehicle-id{display:flex;align-items:baseline;justify-content:space-between;gap:6px;min-width:0}
        .vehicle-id strong{display:block;font-family:Consolas,monospace;font-size:.8rem;line-height:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .vehicle-id span{color:var(--muted);font-size:.64rem;line-height:1;white-space:nowrap}
        .vehicle-meta{color:var(--muted);font-size:.64rem;line-height:1.08;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .vehicle-live{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:5px;font-size:.64rem;color:#c7d7ec}
        .vehicle-live span{display:grid;gap:1px;padding:4px 5px;border-radius:8px;background:rgba(2,6,23,.28)}
        .vehicle-live small{display:block;color:#9fb0c8;font-size:.52rem;line-height:1;letter-spacing:.05em;text-transform:uppercase}
        .vehicle-live strong{color:#fff;font-size:.68rem;line-height:1;white-space:nowrap}
        .vehicle form{display:grid}
        .vehicle .btn-success{width:100%;min-height:38px;padding:7px 4px;border-radius:10px;font-size:.63rem;line-height:1.05}
        .vehicle-empty{padding:12px;border-radius:12px;border:1px dashed var(--border);color:var(--muted);text-align:center;font-size:.74rem}
        body.sidebar-left-collapsed .sidebar-left{grid-template-rows:auto 1fr}
        body.sidebar-left-collapsed .sidebar-left .sidebar-brand{padding:12px 8px 10px}
        body.sidebar-left-collapsed .sidebar-left .sidebar-head-row{flex-direction:column;justify-content:center;gap:12px}
        body.sidebar-left-collapsed .sidebar-left .sidebar-kicker{justify-content:center}
        body.sidebar-left-collapsed .sidebar-left .sidebar-kicker span:last-child,
        body.sidebar-left-collapsed .sidebar-left .sidebar-brand h2,
        body.sidebar-left-collapsed .sidebar-left .sidebar-brand p,
        body.sidebar-left-collapsed .sidebar-left .sidebar-search,
        body.sidebar-left-collapsed .sidebar-left .sidebar-actions .side-text,
        body.sidebar-left-collapsed .sidebar-left .sidebar-actions .side-pill,
        body.sidebar-left-collapsed .sidebar-left .sidebar-actions .side-arrow,
        body.sidebar-left-collapsed .sidebar-left .stats-title,
        body.sidebar-left-collapsed .sidebar-left .stats .stat-info{display:none}
        body.sidebar-left-collapsed .sidebar-left .sidebar-card{border-top-color:rgba(255,255,255,.04)}
        body.sidebar-left-collapsed .sidebar-left .sidebar-actions,
        body.sidebar-left-collapsed .sidebar-left .stats{padding:10px 8px;gap:8px}
        body.sidebar-left-collapsed .sidebar-left .side-action,
        body.sidebar-left-collapsed .sidebar-left .side-link,
        body.sidebar-left-collapsed .sidebar-left .stat{display:flex;align-items:center;justify-content:center;min-height:48px;padding:0;border:0;border-radius:14px;background:transparent}
        body.sidebar-left-collapsed .sidebar-left .side-action.is-active{background:linear-gradient(180deg,rgba(56,189,248,.18),rgba(37,99,235,.1))}
        body.sidebar-left-collapsed .sidebar-left .side-action.is-active::before{left:50%;top:6px;bottom:auto;width:28px;height:3px;transform:translateX(-50%);border-radius:999px}
        body.sidebar-left-collapsed .sidebar-left .stat:hover,
        body.sidebar-left-collapsed .sidebar-left .side-action:hover,
        body.sidebar-left-collapsed .sidebar-left .side-link:hover{background:rgba(8,15,29,.16)}
        body.sidebar-left-collapsed .sidebar-left .side-icon,
        body.sidebar-left-collapsed .sidebar-left .stat-icon{width:38px;height:38px;margin:0}
        body.sidebar-left-collapsed .sidebar-left .side-action:hover::after,
        body.sidebar-left-collapsed .sidebar-left .side-link:hover::after{
            content:attr(aria-label);
            position:absolute;
            left:calc(100% + 10px);
            top:50%;
            transform:translateY(-50%);
            background:#0f172a;
            color:#fff;
            padding:8px 12px;
            border-radius:10px;
            font-size:.75rem;
            font-weight:600;
            white-space:nowrap;
            z-index:100;
            box-shadow:0 10px 25px rgba(0,0,0,.4);
            pointer-events:none;
            border:1px solid rgba(255,255,255,.1)
        }

        body.sidebar-right-collapsed .sidebar-right{grid-template-rows:auto 1fr}
        body.sidebar-right-collapsed .sidebar-right .vehicles-header{padding:12px 8px 14px;display:grid;justify-items:center;align-content:start;gap:14px}
        body.sidebar-right-collapsed .sidebar-right .sidebar-head-row{flex-direction:column;justify-content:center;gap:12px}
        body.sidebar-right-collapsed .sidebar-right .sidebar-kicker{justify-content:center}
        body.sidebar-right-collapsed .sidebar-right .sidebar-kicker span:last-child,
        body.sidebar-right-collapsed .sidebar-right .vehicles-header p,
        body.sidebar-right-collapsed .sidebar-right .vehicles-summary,
        body.sidebar-right-collapsed .sidebar-right .vehicles-body{display:none}
        body.sidebar-right-collapsed .sidebar-right .vehicles-header h2{margin:0;font-size:.8rem;font-weight:800;line-height:1;writing-mode:vertical-rl;transform:rotate(180deg);letter-spacing:.22em;text-transform:uppercase;color:#dbe7f7;opacity:.86}
        body.sidebar-right-collapsed .sidebar-right .vehicles-header::after{content:"";width:2px;height:72px;background:linear-gradient(180deg,var(--blue),transparent);border-radius:999px;opacity:.42}
        body.sidebar-right-collapsed .sidebar-right .vehicles-header-icon{display:grid !important;place-items:center;width:42px;height:42px;background:rgba(59,130,246,.1);border-radius:14px;border:1px solid rgba(59,130,246,.2);box-shadow:0 8px 20px rgba(0,0,0,.2)}

        .modal[hidden]{display:none}.modal{position:fixed;inset:0;z-index:50;display:grid;place-items:center;padding:18px}.modal-backdrop{position:absolute;inset:0;background:rgba(2,6,23,.72);backdrop-filter:blur(6px)}.modal-card{position:relative;z-index:1;width:min(100%,640px);padding:20px}.modal-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:14px}.modal-head h3{margin:0 0 6px}.modal-head p{margin:0;color:var(--muted)}.selected{display:inline-flex;gap:8px;padding:8px 12px;border-radius:999px;background:rgba(59,130,246,.14);color:#93c5fd;margin-bottom:14px}
        @media (max-width:1100px){body{overflow:auto}.app-grid{grid-template-columns:1fr;height:auto}.sidebar,.sidebar-right{position:static;height:auto;border:1px solid var(--side-line);border-radius:0 0 22px 22px}.main-shell{padding:0 16px 24px;height:auto;overflow:visible}.content-grid{grid-template-columns:1fr}.vehicles-body{overflow:visible}.plan-panel{min-height:auto}}
        @media (max-width:760px){.plan-toolbar{justify-content:stretch}.plan-tools{width:100%;justify-content:space-between}}
        @media (max-width:640px){.main-shell{padding:0 12px 24px}.form-row{grid-template-columns:1fr}.vehicle{grid-template-columns:1fr;text-align:left}.vehicle .btn-success{min-height:34px}}
    </style>
</head>
<body class="sidebar-left-collapsed">
    <div class="page">
        @if (session('status'))
            <div class="toast-stack">
                <div class="toast toast-success" data-toast>
                    <div class="toast-copy">
                        <strong>Operacion completada</strong>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" class="btn btn-ghost toast-close" data-dismiss-toast>Cerrar</button>
                </div>
            </div>
        @endif

        <section class="app-grid">
            <aside class="sidebar sidebar-left">
                <div class="sidebar-brand">
                    <div class="sidebar-head-row">
                        <div class="sidebar-kicker">
                            <span class="sidebar-logo" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M8 6h7a3 3 0 0 1 0 6H8z"></path>
                                    <path d="M8 12v6"></path>
                                </svg>
                            </span>
                            <span>Parking Navigation</span>
                        </div>
                        <button type="button" class="sidebar-toggle" data-toggle-sidebar="left" aria-label="Plegar sidebar izquierdo" aria-expanded="true">
                            <span data-toggle-icon>
                                <svg class="toggle-icon" viewBox="0 0 16 16" aria-hidden="true">
                                    <polyline points="10 3.5 5.5 8 10 12.5"></polyline>
                                </svg>
                            </span>
                        </button>
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
                        <button type="button" class="side-action is-active" data-open-help-modal aria-label="Como funciona">
                            <span class="side-icon">?</span>
                            <span class="side-text">
                                <strong>Como funciona</strong>
                                <span>Ayuda y guia del tablero</span>
                            </span>
                            <span class="side-arrow">></span>
                        </button>
                        <a href="{{ route('layout.editor') }}" class="side-link" aria-label="Editar plano">
                            <span class="side-icon">@</span>
                            <span class="side-text">
                                <strong>Editar plano</strong>
                                <span>Personaliza el parque vehicular</span>
                            </span>
                            <span class="side-pill">New</span>
                        </a>
                        <button type="button" class="side-action" data-open-spot-modal aria-label="Agregar cajón">
                            <span class="side-icon">+</span>
                            <span class="side-text">
                                <strong>Agregar cajón</strong>
                                <span>Alta de nuevo espacio</span>
                            </span>
                            <span class="side-pill">New</span>
                        </button>
                        <a href="{{ route('history.index') }}" class="side-link" aria-label="Historial">
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
                    <section class="panel plan-panel">
                        <div class="plan-toolbar">
                            <div class="plan-toolbar-copy">Ajusta el plano al espacio disponible o usa zoom manual si necesitas mas detalle.</div>
                            <div class="plan-tools">
                                <button type="button" class="plan-tool-btn" data-plan-pan aria-label="Mover plano">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M8 11V5a1 1 0 0 1 2 0v5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M12 10V4a1 1 0 0 1 2 0v6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M16 11V6a1 1 0 0 1 2 0v7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M6 12.5V9a1 1 0 0 1 2 0v6.5l1.2-1.2a1.8 1.8 0 0 1 2.55 0l.25.25c.52.52 1.22.82 1.96.82H18a2 2 0 0 1 2 2v.6a3.8 3.8 0 0 1-1.11 2.69l-1.38 1.38A4 4 0 0 1 14.68 23H11.8a4 4 0 0 1-2.83-1.17l-2.8-2.8A4 4 0 0 1 5 16.2v-2.7a1 1 0 0 1 1-1Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                                <button type="button" class="plan-tool-btn" data-plan-fit>Ajustar</button>
                                <button type="button" class="plan-tool-btn" data-plan-reset>100%</button>
                                <button type="button" class="plan-tool-btn" data-plan-zoom-out>-</button>
                                <span class="plan-scale-pill" data-plan-scale-label>100%</span>
                                <button type="button" class="plan-tool-btn" data-plan-zoom-in>+</button>
                            </div>
                        </div>

                        <div class="plan-shell" data-plan-shell>
                            <div class="plan-stage" data-plan-stage>
                                <div
                                    class="parking-canvas {{ $layout->show_grid ? '' : 'no-grid' }}"
                                    data-plan-canvas
                                    data-canvas-width="{{ $layout->canvas_width }}"
                                    data-canvas-height="{{ $layout->canvas_height }}"
                                    style="width: {{ $layout->canvas_width }}px; height: {{ $layout->canvas_height }}px;"
                                >
                            @foreach ($decorations as $decoration)
                                <div class="layout-item layout-{{ $decoration['type'] ?? 'label' }}" style="left: {{ (int) ($decoration['x'] ?? 0) }}px; top: {{ (int) ($decoration['y'] ?? 0) }}px; width: {{ (int) ($decoration['width'] ?? 100) }}px; height: {{ (int) ($decoration['height'] ?? 40) }}px; transform: rotate({{ (int) ($decoration['rotation'] ?? 0) }}deg);">
                                    @if (($decoration['label'] ?? '') !== '')
                                        <span>{{ $decoration['label'] }}</span>
                                    @endif
                                </div>
                            @endforeach

                            <div class="compass">
                                <span class="compass-mark">N</span>
                                <span class="compass-arrow"></span>
                            </div>

                            @forelse ($spots as $spot)
                                @php
                                    $statusClass = ! $spot->is_active ? 'off' : ($spot->activeSession ? 'busy' : 'free');
                                    $isTightSpot = $spot->layout_width < 92 || $spot->layout_height < 150;
                                    $isCompactSpot = abs($spot->layout_angle) % 180 === 90;
                                    $spotClasses = trim('plan-spot '.$statusClass.($isTightSpot ? ' is-tight' : '').($isCompactSpot ? ' is-compact' : ''));
                                    $displayWidth = $spot->layout_width;
                                    $displayHeight = $spot->layout_height;
                                    $displayLeft = $spot->layout_x;
                                    $displayTop = $spot->layout_y;
                                @endphp
                                @if (! $spot->is_active)
                                    <div class="{{ $spotClasses }}" style="left: {{ $displayLeft }}px; top: {{ $displayTop }}px; width: {{ $displayWidth }}px; height: {{ $displayHeight }}px; --spot-rotation: {{ $spot->layout_angle }}deg; transform: rotate(var(--spot-rotation));">
                                        <div class="plan-spot-code">{{ $spot->code }}</div>
                                        <div class="plan-spot-zone">{{ $spot->zone ?: 'Sin zona' }}</div>
                                        <div class="plan-badge">Fuera</div>
                                    </div>
                                @elseif ($spot->activeSession)
                                    @php
                                        $activeMinutes = $spot->activeSession->billedMinutes();
                                        $activeHours = intdiv($activeMinutes, 60);
                                        $activeRemainder = $activeMinutes % 60;
                                    @endphp
                                    <div class="{{ $spotClasses }}" style="left: {{ $displayLeft }}px; top: {{ $displayTop }}px; width: {{ $displayWidth }}px; height: {{ $displayHeight }}px; --spot-rotation: {{ $spot->layout_angle }}deg; transform: rotate(var(--spot-rotation));">
                                        <div class="plan-spot-code">{{ $spot->code }}</div>
                                        <div class="plan-spot-zone">{{ $spot->zone ?: 'Sin zona' }}</div>
                                        <div class="plan-badge">Ocupado</div>
                                        <div class="plate-chip">{{ $spot->activeSession->plate_number }}</div>
                                        <div class="spot-live live-session" data-entry-time="{{ $spot->activeSession->entry_time->toIso8601String() }}" data-hourly-rate="{{ $spot->activeSession->hourly_rate }}">
                                            <span>Tiempo <strong data-live-elapsed>{{ sprintf('%02d:%02d:00', $activeHours, $activeRemainder) }}</strong></span>
                                            <span>Costo <strong data-live-cost>${{ number_format($spot->activeSession->currentAmount(), 2) }}</strong></span>
                                        </div>
                                    </div>
                                @else
                                    <button type="button" class="{{ $spotClasses }} spot-trigger" data-spot-id="{{ $spot->id }}" data-spot-code="{{ $spot->code }}" data-spot-zone="{{ $spot->zone ?: 'Sin zona' }}" style="left: {{ $displayLeft }}px; top: {{ $displayTop }}px; width: {{ $displayWidth }}px; height: {{ $displayHeight }}px; --spot-rotation: {{ $spot->layout_angle }}deg; transform: rotate(var(--spot-rotation));">
                                        <div class="plan-spot-code">{{ $spot->code }}</div>
                                        <div class="plan-spot-zone">{{ $spot->zone ?: 'Sin zona' }}</div>
                                        <div class="plan-badge">Libre</div>
                                        <div class="plan-spot-note">Clic para registrar</div>
                                    </button>
                                @endif
                            @empty
                                <p style="position:absolute;left:24px;top:24px;color:var(--muted)">No hay cajones registrados todavia.</p>
                            @endforelse
                                </div>
                            </div>
                        </div>
                    </section>
                </section>
            </main>

            <aside class="sidebar sidebar-right vehicles-panel">
                <div class="vehicles-header">
                    <div class="sidebar-head-row">
                        <div class="sidebar-kicker">
                            <span class="sidebar-logo" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M4 14h16"></path>
                                    <path d="M6.5 14l1.4-4.2A2 2 0 0 1 9.8 8h4.4a2 2 0 0 1 1.9 1.8l1.4 4.2"></path>
                                    <circle cx="8" cy="16.5" r="1.5"></circle>
                                    <circle cx="16" cy="16.5" r="1.5"></circle>
                                </svg>
                            </span>
                            <span>Parking Status</span>
                        </div>
                        <button type="button" class="sidebar-toggle" data-toggle-sidebar="right" aria-label="Plegar sidebar derecho" aria-expanded="true">
                            <span data-toggle-icon>
                                <svg class="toggle-icon" viewBox="0 0 16 16" aria-hidden="true">
                                    <polyline points="6 3.5 10.5 8 6 12.5"></polyline>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="vehicles-header-icon" style="display: none;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue);"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9l-2.5-.6-1.1-2.2c-.3-.7-1.2-1.3-2-1.3H9.1c-.8 0-1.7.6-2 1.3L6 10.5l-2.5.6c-.8.2-1.5 1-1.5 1.9v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    </div>
                    <h2>Vehiculos dentro</h2>
                    <p>Monitorea en tiempo real quienes siguen dentro del estacionamiento.</p>
                </div>

                <div class="vehicles-summary">
                    <div class="vehicles-summary-row">
                        <span>Vehiculos activos</span>
                        <strong>{{ $occupiedSpots->count() }}</strong>
                    </div>
                </div>

                <div class="vehicles-body">
                    <div class="vehicle-list">
                        @forelse ($occupiedSpots as $spot)
                            @php
                                $vehicleMinutes = $spot->activeSession->billedMinutes();
                                $vehicleHours = intdiv($vehicleMinutes, 60);
                                $vehicleRemainder = $vehicleMinutes % 60;
                            @endphp
                            <article class="vehicle">
                                <div class="vehicle-main">
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
                                    <div
                                        class="vehicle-live live-session"
                                        data-entry-time="{{ $spot->activeSession->entry_time->toIso8601String() }}"
                                        data-hourly-rate="{{ $spot->activeSession->hourly_rate }}"
                                    >
                                        <span>
                                            <small>Tiempo</small>
                                            <strong data-live-elapsed>{{ sprintf('%02d:%02d:00', $vehicleHours, $vehicleRemainder) }}</strong>
                                        </span>
                                        <span>
                                            <small>Costo</small>
                                            <strong data-live-cost>${{ number_format($spot->activeSession->currentAmount(), 2) }}</strong>
                                        </span>
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
                </div>
            </aside>
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
        const liveSessions = document.querySelectorAll('.live-session');
        const toast = document.querySelector('[data-toast]');
        const spotIdInput = document.getElementById('parking_spot_id');
        const selectedSpotLabel = document.getElementById('selected-spot-label');
        const spotActionButton = document.querySelector('[data-open-spot-modal]');
        const sidebarStatsTitle = document.querySelector('.sidebar-card h2[style]');
        const sidebarToggleButtons = document.querySelectorAll('[data-toggle-sidebar]');
        const desktopSidebarMedia = window.matchMedia('(max-width: 1100px)');
        const planShell = document.querySelector('[data-plan-shell]');
        const planStage = document.querySelector('[data-plan-stage]');
        const planCanvas = document.querySelector('[data-plan-canvas]');
        const planScaleLabel = document.querySelector('[data-plan-scale-label]');
        const planPanButton = document.querySelector('[data-plan-pan]');
        const planFitButton = document.querySelector('[data-plan-fit]');
        const planResetButton = document.querySelector('[data-plan-reset]');
        const planZoomOutButton = document.querySelector('[data-plan-zoom-out]');
        const planZoomInButton = document.querySelector('[data-plan-zoom-in]');
        const sidebarToggleIcons = {
            left: '<svg class="toggle-icon" viewBox="0 0 16 16" aria-hidden="true"><polyline points="10 3.5 5.5 8 10 12.5"></polyline></svg>',
            right: '<svg class="toggle-icon" viewBox="0 0 16 16" aria-hidden="true"><polyline points="6 3.5 10.5 8 6 12.5"></polyline></svg>',
        };
        const minPlanScale = 0.45;
        const maxPlanScale = 2.4;
        let planScale = 1;
        let planScaleX = 1;
        let planScaleY = 1;
        let planAutoFit = true;
        let planPanMode = false;
        let planDragging = false;
        let planPointerId = null;
        let planDragStartX = 0;
        let planDragStartY = 0;
        let planDragScrollLeft = 0;
        let planDragScrollTop = 0;

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

        function updateSidebarToggleButton(side) {
            const collapsed = document.body.classList.contains(`sidebar-${side}-collapsed`);
            const button = document.querySelector(`[data-toggle-sidebar="${side}"]`);

            if (!button) {
                return;
            }

            const icon = button.querySelector('[data-toggle-icon]');
            button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            button.setAttribute('aria-label', collapsed
                ? `Expandir sidebar ${side === 'left' ? 'izquierdo' : 'derecho'}`
                : `Plegar sidebar ${side === 'left' ? 'izquierdo' : 'derecho'}`);

            if (icon) {
                const iconKey = side === 'left'
                    ? (collapsed ? 'right' : 'left')
                    : (collapsed ? 'left' : 'right');
                icon.innerHTML = sidebarToggleIcons[iconKey];
            }
        }

        function setSidebarCollapsed(side, collapsed) {
            const className = `sidebar-${side}-collapsed`;
            document.body.classList.toggle(className, collapsed);
            updateSidebarToggleButton(side);
        }

        function syncSidebarLayout() {
            if (desktopSidebarMedia.matches) {
                document.body.classList.remove('sidebar-left-collapsed', 'sidebar-right-collapsed');
                updateSidebarToggleButton('left');
                updateSidebarToggleButton('right');
                requestAnimationFrame(syncPlanViewport);
                return;
            }

            setSidebarCollapsed('left', true);
            setSidebarCollapsed('right', false);
            requestAnimationFrame(syncPlanViewport);
        }

        function updatePlanToolbar() {
            if (!planScaleLabel) {
                return;
            }

            const scaleText = Math.abs(planScaleX - planScaleY) < 0.01
                ? `${Math.round(planScaleX * 100)}%`
                : `${Math.round(planScaleX * 100)}x${Math.round(planScaleY * 100)}`;

            planScaleLabel.textContent = scaleText;
            planPanButton?.classList.toggle('is-active', planPanMode);
            planFitButton?.classList.toggle('is-active', planAutoFit);
            planResetButton?.classList.toggle('is-active', !planAutoFit && Math.abs(planScale - 1) < 0.01);
        }

        function updatePlanPanState() {
            if (!planShell) {
                return;
            }

            planShell.classList.toggle('is-pan-mode', planPanMode);
            planShell.classList.toggle('is-panning', planPanMode && planDragging);
            updatePlanToolbar();
        }

        function applyPlanTransform(scaleX, scaleY) {
            if (!planStage || !planCanvas) {
                return;
            }

            const baseWidth = Number(planCanvas.dataset.canvasWidth);
            const baseHeight = Number(planCanvas.dataset.canvasHeight);
            planScaleX = Math.max(minPlanScale, Math.min(maxPlanScale, scaleX));
            planScaleY = Math.max(minPlanScale, Math.min(maxPlanScale, scaleY));
            planScale = Math.min(planScaleX, planScaleY);

            planStage.style.width = `${Math.round(baseWidth * planScaleX)}px`;
            planStage.style.height = `${Math.round(baseHeight * planScaleY)}px`;
            planCanvas.style.transform = `scale(${planScaleX}, ${planScaleY})`;

            updatePlanToolbar();
        }

        function applyPlanScale(scale) {
            const uniformScale = Math.max(minPlanScale, Math.min(maxPlanScale, scale));
            applyPlanTransform(uniformScale, uniformScale);
        }

        function fitPlanToViewport() {
            if (!planShell || !planCanvas) {
                return;
            }

            const baseWidth = Number(planCanvas.dataset.canvasWidth);
            const baseHeight = Number(planCanvas.dataset.canvasHeight);
            const availableWidth = Math.max(planShell.clientWidth - 2, 0);
            const availableHeight = Math.max(planShell.clientHeight - 2, 0);
            const scale = Math.min(availableWidth / baseWidth, availableHeight / baseHeight);

            applyPlanScale(scale);
        }

        function syncPlanViewport() {
            if (!planShell || !planCanvas) {
                return;
            }

            if (planAutoFit) {
                fitPlanToViewport();
                return;
            }

            applyPlanScale(planScale);
        }

        function stopPlanDragging() {
            if (planShell && planPointerId !== null) {
                planShell.releasePointerCapture?.(planPointerId);
            }

            planDragging = false;
            planPointerId = null;
            updatePlanPanState();
        }

        function formatElapsed(totalSeconds) {
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            return [hours, minutes, seconds]
                .map((value) => String(value).padStart(2, '0'))
                .join(':');
        }

        function updateLiveSessions() {
            const now = new Date();

            liveSessions.forEach((session) => {
                const entryTime = new Date(session.dataset.entryTime);
                const hourlyRate = Number(session.dataset.hourlyRate);
                const elapsedSeconds = Math.max(0, Math.floor((now - entryTime) / 1000));
                const billedMinutes = Math.max(1, Math.ceil(elapsedSeconds / 60));
                const billedHours = Math.ceil(billedMinutes / 60);
                const currentAmount = billedHours * hourlyRate;
                const elapsedTarget = session.querySelector('[data-live-elapsed]');
                const costTarget = session.querySelector('[data-live-cost]');

                if (elapsedTarget) {
                    elapsedTarget.textContent = formatElapsed(elapsedSeconds);
                }

                if (costTarget) {
                    costTarget.textContent = '$' + currentAmount.toFixed(2);
                }
            });
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

        function closeToast() {
            if (toast) {
                toast.closest('.toast-stack')?.remove();
            }
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

        document.querySelectorAll('[data-dismiss-toast]').forEach((button) => {
            button.addEventListener('click', closeToast);
        });

        sidebarToggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (desktopSidebarMedia.matches) {
                    return;
                }

                const side = button.dataset.toggleSidebar;
                const collapsed = document.body.classList.contains(`sidebar-${side}-collapsed`);
                setSidebarCollapsed(side, !collapsed);
                requestAnimationFrame(() => requestAnimationFrame(syncPlanViewport));
            });
        });

        planFitButton?.addEventListener('click', () => {
            planAutoFit = true;
            fitPlanToViewport();
        });

        planPanButton?.addEventListener('click', () => {
            planPanMode = !planPanMode;
            stopPlanDragging();
        });

        planResetButton?.addEventListener('click', () => {
            planAutoFit = false;
            applyPlanScale(1);
        });

        planZoomOutButton?.addEventListener('click', () => {
            planAutoFit = false;
            applyPlanScale(planScale - 0.1);
        });

        planZoomInButton?.addEventListener('click', () => {
            planAutoFit = false;
            applyPlanScale(planScale + 0.1);
        });

        planShell?.addEventListener('pointerdown', (event) => {
            if (!planPanMode || event.button !== 0) {
                return;
            }

            planDragging = true;
            planPointerId = event.pointerId;
            planDragStartX = event.clientX;
            planDragStartY = event.clientY;
            planDragScrollLeft = planShell.scrollLeft;
            planDragScrollTop = planShell.scrollTop;
            planShell.setPointerCapture?.(event.pointerId);
            updatePlanPanState();
            event.preventDefault();
        });

        window.addEventListener('pointermove', (event) => {
            if (!planDragging || !planShell || (planPointerId !== null && event.pointerId !== planPointerId)) {
                return;
            }

            const deltaX = event.clientX - planDragStartX;
            const deltaY = event.clientY - planDragStartY;

            planShell.scrollLeft = planDragScrollLeft - deltaX;
            planShell.scrollTop = planDragScrollTop - deltaY;
            event.preventDefault();
        });

        ['pointerup', 'pointercancel'].forEach((eventName) => {
            window.addEventListener(eventName, (event) => {
                if (!planDragging || (planPointerId !== null && event.pointerId !== planPointerId)) {
                    return;
                }

                stopPlanDragging();
            });
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (!checkInModal.hidden) closeModal('check-in-modal');
                if (!spotModal.hidden) closeModal('spot-modal');
                if (!helpModal.hidden) closeModal('help-modal');
                if (planPanMode) {
                    planPanMode = false;
                    stopPlanDragging();
                }
            }
        });

        if (typeof desktopSidebarMedia.addEventListener === 'function') {
            desktopSidebarMedia.addEventListener('change', syncSidebarLayout);
        } else {
            desktopSidebarMedia.addListener(syncSidebarLayout);
        }

        window.addEventListener('resize', syncPlanViewport);

        updateLiveSessions();
        syncSidebarLayout();
        syncPlanViewport();
        updatePlanPanState();
        setInterval(updateLiveSessions, 1000);
        if (toast) {
            setTimeout(closeToast, 3200);
        }
        syncBodyScroll();
    </script>
</body>
</html>
