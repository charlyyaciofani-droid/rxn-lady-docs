# Limpieza de Salida Visible y Logs Operativos
**Fecha:** 2026-03-22  16:48

## Contexto Heredado
Tras corregir la regresión en *Pedidos* aplicando un rollback del manipulador de buffers (flush) añadido en `csv/modelo.php`, el flujo transaccional y de comunicación con la API de Tango se recuperó al 100%. Sin embargo, en el visor/Iframe destinado a fungir como consola, persisten textos legacy y salidas accidentales generadas nativamente por las funciones de back-end.

## Salidas Detectadas en Pedidos y Facturas
Se escaneó `csv/modelo.php` buscando impresiones crudas hacia la interfaz (`echo` y `print_r`) resultando en la siguiente clasificación:

### A) Ruido Legacy y Salidas Técnicas Accidentales (Exclusivo en Pedidos)
Mensajes de depuración programados originalmente para trazar la ejecución de las funciones, completamente irrelevantes e incomprensibles operativamente:
- `echo 'entro acá?';`
- `echo 'Pedido: ' ... ' Orden: ' ...`
- `echo '¿Entro al artículo?<br>';`
- `echo 'Artículo en encabezado: <br>';`
- `print_r($data_string);`
- `echo 'Llego hasta acá: <br>';`
- `echo 'Detalle de la respuesta <br>';`
- `print_r($response);`

### B) Mensajes Útiles Operativamente
Textos informativos clave para el estado del proceso, pero sin formato:
- `echo 'El pedido [X] para el cliente [Y] ya existe<br>'`
- `echo "No hay archivos de pedidos para procesar"`
- *Asociados a carga de clientes extra/artículos:* "Existe un error", "Se grabó correctamente el cliente...", etc.

## Estrategia Elegida
**No realizar inyecciones nuevas que fuercen vaciados pre-calculados**. En su lugar, aplicar una política de supresión y unificación puramente HTML.

## Cambios a Aplicar (Plan para la siguiente Fase)
1. **Qué se elimina/suprime**: Todo el bloque listado en la sección "Ruido Legacy" (puntos A). Dichos `echo` y `print_r` serán eliminados o rigurosamente comentados.
2. **Qué se deja visible**: Todo el bloque listado en "Mensajes Útiles" (puntos B).
3. **Cómo se formatea**: En lugar de texto plano con `<br>`, se cambiará la instrucción `echo` original por su equivalente envuelta en un contenedor div estandarizado y en colores operacionales (Naranja para advertencias, Rojo para errores, Verde para éxitos).
   - Ejemplo: `<div style="color: #ff9800; font-family: monospace; font-size: 14px; padding: 6px; border-bottom: 1px solid #444;">[PID-XXX] ⚠ El pedido ya existe</div>`

## Restricciones Respetadas
- **Ninguna** introducción de código regulador de buffer (`flush()` / `ob_flush()`). El servidor y navegador dictarán el ritmo de refresco.
- **Facturas** no se ve comprometido ya que no posee ecos residuales pre-transaccionales; mantendrá su etiqueta final configurada en la iteración previa.

## Validación Manual Esperada
Al aplicar este parche de limpieza, el usuario que procese Pedidos no debe ver en ningún momento palabras sueltas como "Llego acá", ni volcados largos de arrays matriciales. La pantalla debe comportarse silente como una terminal limpia y, solo reportará advertencia visible sobre duplicidad operativa u éxito/falla pos-procesamiento.
## Riesgos Residuales
Nulos. Se trata exclusivamente de comentarios de purga en sintaxis front (echo HTML). La variable lógica y payload para cURL no interfiere con las impresiones directas en PHP contemporáneo.
