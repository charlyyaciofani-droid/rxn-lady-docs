<?php
require_once __DIR__ . '/auth/guard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - RXN Lady API</title>
    <!-- Prevención FOUC Tema -->
    <script>
        if (localStorage.getItem('rxn-theme') === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>
    <link href="rxn-ui.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Estilos específicos para las tarjetas arrastrables del dashboard */
        .rxn-card.draggable {
            cursor: grab;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        .rxn-card.draggable:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background-color: var(--rxn-table-hover);
        }
        .rxn-card.draggable:active {
            cursor: grabbing;
        }
        .rxn-card.draggable.dragging {
            opacity: 0.5;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="rxn-container rxn-mt-5">
    <div class="rxn-flex-between rxn-mb-4">
        <div style="display: flex; align-items: center;">
            <img src="logo.png" alt="Re@xion Logo" class="rxn-logo-inline" onerror="this.style.display='none'">
            <div>
                <h1 style="margin-top: 0; margin-bottom: 5px;">Re@xion - Lady API</h1>
                <p class="rxn-text-muted" style="font-size: 1.25rem; margin-top: 0; margin-bottom: 0;">Panel de Control Principal</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px; align-items: stretch;">
            <!-- Theme Toggle -->
            <button id="rxn-theme-btn" class="rxn-btn rxn-btn-secondary" title="Alternar Tema" style="padding: 6px 12px;">
                <i class="bi bi-moon-stars"></i>
            </button>
            <a href="auth/logout.php" class="rxn-btn" style="background-color: transparent; color: #dc3545; border: 1px solid #dc3545; padding: 6px 12px; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <div class="rxn-grid" id="dashboardGrid">
        
        <!-- 1. Módulo CSV -->
        <div class="rxn-card draggable" data-id="1" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-file-earmark-spreadsheet rxn-card-icon"></i>
                <h3 class="rxn-card-title">Procesar CSV</h3>
                <p class="rxn-card-text">Lectura, procesamiento y carga de comprobantes masivos hacia el sistema Tango.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="csv/index.php" class="rxn-btn rxn-btn-primary rxn-btn-block">
                    Ir a Procesar CSV <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 2. Módulo Rechazar comprobantes pendientes -->
        <div class="rxn-card draggable" data-id="2" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-x-circle rxn-card-icon"></i>
                <h3 class="rxn-card-title">Rechazar comprobantes pendientes</h3>
                <p class="rxn-card-text">Cambio de estado masivo a "Rechazado" en Tango para liberar trabas tributarias.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="csv/index_rechazar_pendientes.php" class="rxn-btn rxn-btn-secondary rxn-btn-block" style="border-color: #dc3545; color: #dc3545;">
                    Ir a Rechazar <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 3. Módulo Copia Facturador -->
        <div class="rxn-card draggable" data-id="3" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-files rxn-card-icon"></i>
                <h3 class="rxn-card-title">Copia Facturador</h3>
                <p class="rxn-card-text">Gestión de duplicado y asignación de facturación según perfiles definidos.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="copiaFacturas/index.php" class="rxn-btn rxn-btn-secondary rxn-btn-block">
                    Ir a Copias <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 4. Módulo Reprocesos -->
        <div class="rxn-card draggable" data-id="4" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-arrow-clockwise rxn-card-icon"></i>
                <h3 class="rxn-card-title">Reprocesar</h3>
                <p class="rxn-card-text">Reintento de ingreso para comprobantes retenidos o con error de pre-validación.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="csv/index_reprocesos.php" class="rxn-btn rxn-btn-secondary rxn-btn-block">
                    Ir a Reprocesos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 5. Módulo Limpieza -->
        <div class="rxn-card draggable" data-id="5" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-trash rxn-card-icon"></i>
                <h3 class="rxn-card-title">Limpieza de Archivos</h3>
                <p class="rxn-card-text">Herramienta para purgado y borrado de registros pendientes o estancados.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="limpiarArchivos/index.php" class="rxn-btn rxn-btn-secondary rxn-btn-block">
                    Ir a Limpieza <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 6. Módulo Gestión de Usuarios -->
        <div class="rxn-card draggable" data-id="6" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-people rxn-card-icon"></i>
                <h3 class="rxn-card-title">Gestión de Usuarios</h3>
                <p class="rxn-card-text">Alta, baja y modificación de accesos al sistema. Control de roles.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="usuarios/index.php" class="rxn-btn rxn-btn-secondary rxn-btn-block">
                    Ir a Usuarios <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 7. Módulo Configuración -->
        <div class="rxn-card draggable" data-id="7" draggable="true">
            <div class="rxn-card-body rxn-text-center">
                <i class="bi bi-gear-fill rxn-card-icon"></i>
                <h3 class="rxn-card-title">Configuración</h3>
                <p class="rxn-card-text">Ajustes generales, rutas de XML locales, tokens TiendaNube y facturadores preferidos.</p>
            </div>
            <div class="rxn-card-footer">
                <a href="configuraciones/index.php" class="rxn-btn rxn-btn-secondary rxn-btn-block">
                    Ir a Configuración <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

    </div>

    <div class="rxn-text-center rxn-text-muted" style="margin-top: 50px; margin-bottom: 20px; font-size: 14px;">
        <small>&copy; 2026 Re@xion - Desarrollo de soluciones para Tango</small>
    </div>

</div>

<script>
    // Logic Tema Global
    const themeBtn = document.getElementById('rxn-theme-btn');
    const themeIcon = themeBtn.querySelector('i');
    
    if (document.documentElement.getAttribute('data-theme') === 'light') {
        themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
    }

    themeBtn.addEventListener('click', () => {
        let theme = document.documentElement.getAttribute('data-theme');
        if (theme === 'light') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('rxn-theme', 'dark');
            themeIcon.classList.replace('bi-sun', 'bi-moon-stars');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('rxn-theme', 'light');
            themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
        }
    });

    // Logic Dashboard Drag & Drop + Click
    document.addEventListener("DOMContentLoaded", () => {
        const grid = document.getElementById('dashboardGrid');
        const cards = Array.from(grid.querySelectorAll('.rxn-card.draggable'));
        
        // El orden inicial obligatorio estipulado por el orquestador
        const ordenPorDefecto = ["1", "2", "3", "4", "5", "6", "7"];
        
        let ordenGuardado = localStorage.getItem('rxn-dashboard-order');
        if (ordenGuardado) {
            try {
                ordenGuardado = JSON.parse(ordenGuardado);
                // Validación básica para evitar corrupciones y faltantes
                if(Array.isArray(ordenGuardado) && ordenGuardado.length === cards.length) {
                    reordenarDOM(ordenGuardado);
                } else {
                    reordenarDOM(ordenPorDefecto);
                }
            } catch(e) {
                reordenarDOM(ordenPorDefecto);
            }
        } else {
            reordenarDOM(ordenPorDefecto);
        }
        
        function reordenarDOM(orden) {
            orden.forEach(id => {
                const card = cards.find(c => c.getAttribute('data-id') === id);
                if(card) {
                    grid.appendChild(card);
                }
            });
        }

        let draggedItem = null;
        let isDragging = false;
        
        cards.forEach(card => {
            // Toda la tarjeta se vuelve clickeable y lleva al módulo
            card.addEventListener('click', (e) => {
                if (isDragging) {
                    e.preventDefault();
                    return;
                }
                // Evitamos doble disparo si el usuario hizo click justo en el enlace <a href>
                if (e.target.closest('a')) return;
                
                const link = card.querySelector('a.rxn-btn');
                if (link && link.getAttribute('href')) {
                    window.location.href = link.getAttribute('href');
                }
            });

            // Native HTML5 Drag and Drop events
            card.addEventListener('dragstart', function(e) {
                draggedItem = this;
                isDragging = true;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                // Soporte cross-browser para que el feedback visual del drag se fije bien
                setTimeout(() => this.style.opacity = '0', 0);
            });

            card.addEventListener('dragend', function() {
                // Timeout para prevenir que el "soltar" accione el "click" de navegación
                setTimeout(() => { isDragging = false; }, 150); 
                
                this.classList.remove('dragging');
                this.style.opacity = '1';
                draggedItem = null;
                
                // Extraer el nuevo orden y guardarlo en localStorage
                const nuevoOrden = Array.from(grid.querySelectorAll('.rxn-card.draggable')).map(c => c.getAttribute('data-id'));
                localStorage.setItem('rxn-dashboard-order', JSON.stringify(nuevoOrden));
            });

            card.addEventListener('dragover', function(e) {
                e.preventDefault(); // Permitir el Drop
                e.dataTransfer.dropEffect = 'move';
                
                if (this !== draggedItem && draggedItem !== null) {
                    const bounding = this.getBoundingClientRect();
                    // Obtener posición vertical vs horizontal
                    const offset = bounding.y + (bounding.height / 2);
                    
                    // Efecto reordenamiento en vivo
                    if (e.clientY - offset > 0) {
                        this.after(draggedItem);
                    } else {
                        this.before(draggedItem);
                    }
                }
            });
            
            card.addEventListener('dragenter', function(e) {
                e.preventDefault();
            });
        });
    });
</script>
</body>
</html>
