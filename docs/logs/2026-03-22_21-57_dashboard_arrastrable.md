# Reordenamiento de Dashboard con Drag & Drop y Persistencia

## Contexto
El menú principal (`index.php`) contenía los accesos correctos, pero la navegabilidad era rudimentaria: obligado a presionar el `<a href>` ínfimo (botón final) y con un orden dictaminado por código duro imprecedible que entorpecía la operación rápida para módulos críticos (Procesar CSV / Descartar Pendientes). Se requirió elevar el diseño para proveer interactividad táctil/mouse e introducir el patrón *Drag and Drop* nativo apoyado en LocalStorage.

## Problema Actual
1. Área de click restringida al botón, limitando la UX de las `.rxn-card`.
2. Orden fijo e inalterable.
3. El módulo fundamental "Descartar Pendientes" tenía naming confuso.

## Archivos Afectados
- `index.php` (Core UI del sistema).

## Implementación Realizada
- **HTML:** A las 7 tarjetas se les inyectó la clase `.draggable` y el atributo `data-id="X"` numerados del 1 al 7 estrictamente bajo el orden rector dictado.
- **Wording:** La tarjeta N° 2 fue reescrita integralmente refiriéndose a ella visualmente como `"Rechazar comprobantes pendientes"`.
- **Navegabilidad (UX):** Un bloque JavaScript puro aísla mediante delegación de eventos el tap/click global de la tarjeta, emulando la redirección del ancla interna `<a>` *siempre que* no se encuentre en un *state* activo de "arrastre".
- **Drag & Drop:**
  - Uso API HTML5: `dragstart`, `dragend`, `dragover`, y `dragenter`.
  - Mutación del DOM en vivo al superponer una tarjeta sobre el `boundingBox` de otra.
  - El soltar (`dragend`) dispara la captura en Array de los nuevos ids, inyectándolos encapsulados con JSON en `localStorage.getItem('rxn-dashboard-order')`.
- **Bootloader (Persistencia):**
  - Al iniciar `DOMContentLoaded`, el DOM se pinta por defecto o leyendo la caché; y por mutación directa (`appendChild`), posiciona las `.rxn-card` correctas en la grilla visual. Si falla o es errático, recarga el Hardcode de fallback.

## Pruebas Mínimas Obligatorias
- [x] Inicia en Caché vacío mostrando el Set 1 ➡ 7 impecable.
- [x] Agarre Tarjeta 6 y soltado entre 1-2. Se actualiza DOM. Recarga: Persiste Layout (6 entre 1 y 2).
- [x] Borrado de localStorage. Recarga: Vuelve a 1 ➡ 7.
- [x] Click en cabecera de la Tarjeta 1. Transita hacia `/csv/index.php`. No choca con el arrastre gracias a delay asincrónico `setTimeout`.

## Resultado y Observaciones
Un menú completamente dinámico, escalable con JS puro sin encolar basuras externas de npm ni saturar al cliente. CSS intacto preservando variables del tema Light/Dark.

> Iteración concluida bajo entorno de control de calidad. 
**Commit:** `feat: dashboard reordenable con persistencia local`
