# Análisis de Exposición de Respuesta API en UI

## Fase 1: Análisis de Métodos

### 1. `ingresoPedido` e `ingresoFactura`
Ambos métodos se encuentran en `csv/modelo.php` y son llamados mediante un bucle principal en el método `procesoPedidos()`.
- Se detectaron ambas llamadas alrededor de la línea 830 (para pedidos) y la línea 875 (para facturas).
- Consumen la API de Tango a través de `curl_init()`, cargando el resultado transaccional en la variable de clase `$this->mensaje_api`.

### 2. Estructura de la Respuesta (API de Tango)
La variable `$this->mensaje_api` es decodificada como array. Sus métricas principales cambian levemente según si es Pedido o Factura:
- **Pedidos:**
  - Éxito evaluado en: `$this->mensaje_api['succeeded'] === true`
  - ID en Tango: `$this->mensaje_api['savedId']`
  - Filas afectadas: `$this->mensaje_api['recordAffectedCount']`
  - Errores en: `['message']` y `['exceptionInfo']`
- **Facturas:**
  - Éxito evaluado en: `!empty($this->mensaje_api['Succeeded'])` (S mayúscula)
  - ID en Tango: `$this->mensaje_api['savedId']`
  - Mensaje principal: `$this->mensaje_api['Message']`
  - Información de Comprobantes: `$this->mensaje_api['Comprobantes'][0]['numeroComprobante']` y `['estado']`
  - Error en: `$this->mensaje_api['Comprobantes'][0]['exceptionMessage']`

### 3. Salida Actual
Actualmente, dentro del bucle en `procesoPedidos()`, se arma un mensaje log (ej. `$mensaje_log` o `$mensaje_txt`) que:
- Se guarda en la base de datos vía `$this->ingresoMensajesApi()`.
- Se imprime en texto plano hacia un sumidero local: `fwrite($fh_log, PHP_EOL . "$mensaje_txt");` sobre el archivo `detalle_proceso.txt`.
- **En Interfaz:** ¡Es totalmente mudo! Solo existe un pequeño `echo` cuando un pedido ya existe ("El pedido... ya existe"). El resto del flujo de la API jamás se imprime.

### 4. Punto de Intervención
El momento ideal para interceptar y exponer los datos hacia el visor Iframe es inmediatamente después de armar `$mensaje_log` (para Pedidos) y `$mensaje_txt` (para Facturas), y justo después del bloque de grabado en `detalle_proceso.txt`.
Ahí tenemos en mano la confirmación de éxito/error y las variables con detalle claro.

---

## Fase 2: Propuesta de Implementación

### Estrategia Metodológica
Añadir una instrucción `echo` controlada y estilizada dentro de `procesoPedidos()` para canalizar visualmente lo que antes iba solo a TXT y BD. Dado que el destino (el `target`) de este formulario ya es el `iframe` que acabamos de normalizar, cualquier `echo` asomará prístino como un renglón de terminal en la pantalla del usuario.

### Formateo Visual Simplificado
No introduciremos divs flotantes ni CSS complejos, sino líneas de estado apilables y minimalistas:
- Renderizaremos un `<div>` o párrafo por cada iteración.
- Colores semánticos: verde para éxito (`#4caf50`), rojo para error (`#dc3545`) y naranja para advertencias/duplicados (`#ff9800`).
- Estructura propuesta: `[N° COMPROBANTE] - [ESTADO] - [MENSAJE_RESUMIDO_API]`

**Ejemplo Teórico de Rendición:**
```html
<div style="color: #4caf50; font-family: monospace; padding: 4px; border-bottom: 1px solid #444;">
    [COMP-1234] ✔ Pedido grabado con éxito | ID 44552 | 1 filas afectadas
</div>
<div style="color: #dc3545; font-family: monospace; padding: 4px; border-bottom: 1px solid #444;">
    [COMP-1235] ✘ ERROR API | message=Cliente inexistente o deshabilitado
</div>
```

### Riesgos Detectados
- **Buffer de Salida:** En ciclos muy largos, los navegadores podrían no renderizar los `echo` hasta el final del proceso. Esto se mitiga invocando `flush()` u `ob_flush()` posterior a la impresión del mensaje para que el iframe vomite la línea en tiempo real a su display mientras el bucle sigue corriendo.
- **Riesgo Estructural:** Ninguno. La recolección de variables ya está sanitizada en el código actual, solo se replicará en el `echo`.

**Decisión final sugerida:** IMPLEMENTAR. El cambio es seguro, atómico y cumple de pleno el diseño propuesto.
