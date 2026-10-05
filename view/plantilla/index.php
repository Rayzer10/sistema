<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Plantillas de Trabajo</h2>
        <p>Descarga plantillas editables para documentar tus proyectos de ingenieria de requisitos.</p>
    </div>
    <div class="welcome-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
    <?php
    $plantillas = [
        ['key' => 'ers',          'titulo' => 'ERS Simplificada',         'desc' => 'Especificacion de Requisitos de Software. Documenta de forma estructurada los requisitos funcionales, no funcionales y restricciones de un sistema.', 'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M9 13h6 M9 17h6'],
        ['key' => 'stakeholders', 'titulo' => 'Matriz de Stakeholders',   'desc' => 'Identifica y clasifica a los involucrados del proyecto segun su poder e interes. Incluye estrategias de gestion por cuadrante.', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'],
        ['key' => 'backlog',      'titulo' => 'Tabla de Backlog',         'desc' => 'Lista priorizada de historias de usuario con estimacion, valor de negocio, criterios de aceptacion y estado.', 'icon' => 'M3 4h18 M3 12h18 M3 20h18 M8 4v16 M16 4v16'],
        ['key' => 'riesgos',      'titulo' => 'Matriz de Riesgos',        'desc' => 'Identifica riesgos del proyecto con su probabilidad, impacto, estrategia de mitigacion y responsable.', 'icon' => 'M12 9v2m0 4h.01M5 19h14a2 2 0 0 0 1.84-2.75L13.74 4a2 2 0 0 0-3.48 0L3.16 16.25A2 2 0 0 0 5 19z'],
    ];
    foreach ($plantillas as $p):
    ?>
        <a href="<?= BASE_URL ?>plantilla/descargar/<?= $p['key'] ?>" target="_blank" class="plantilla-card" style="text-decoration: none;">
            <div style="display: flex; align-items: start; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(94,106,210,0.15); color: #5E6AD2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?= $p['icon'] ?>"/></svg>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <h5 style="color: #FFFFFF; font-size: 15px; font-weight: 500; margin: 0 0 6px;"><?= $p['titulo'] ?></h5>
                    <p style="color: #8A8F98; font-size: 13px; line-height: 1.5; margin: 0 0 10px;"><?= $p['desc'] ?></p>
                    <span style="color: #5E6AD2; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Descargar PDF
                    </span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<style>
.plantilla-card {
    background: #1B1B1F;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 18px;
    display: block;
    transition: border-color 0.15s, transform 0.15s;
}
.plantilla-card:hover {
    border-color: rgba(94,106,210,0.4);
    transform: translateY(-1px);
}
</style>
