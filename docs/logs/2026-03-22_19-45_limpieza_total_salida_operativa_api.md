# [Logs y UI] — [Limpieza total operativa y solución de variables en MVC]

## Contexto
El usuario detectó que el frontend operativo de su consola (el `iframe` que aloja a `procesar.php`) se estaba ensuciando con carteles técnicos nativos de PHP:
> `Undefined variable $cant_x_precio_neto en csv/modelo.php (línea 1140)`.

A su vez se nos solicitó dejar la consola de Pedidos y Facturas brindando exclusivamente información funcional de sus apis, eliminando por completo todo rastro de debug, ecos residuales o variables inconsistentes.

## Síntoma
1. **$cant_x_precio_neto indomada:** En el flujo de Pedidos (`buscoPedidoRXN`), en la línea 1140 de `modelo.php` existía un condicional para atrapar precios negativos sobre las variables `$precio`, `$precio_art`, y `$cant_x_precio_neto`. El problema radicaba en que esa última variable sólo pertenecía y se instanciaba en el flujo de **Facturas**, 150 líneas más abajo. Pedidos nunca generaba el neto descontado porque el Tango ya pasaba los cálculos brutos en las ramas del JSON. Es decir, era un bloque IF copiado y pegado erróneo.
2. **Registro de logs nulo:** En la creación de llamadas de cURL de la api (`ingresoPedido`), ante cualquier error HTTP el sistema escupía un `$this->registrarErrorLog(...)`, método que fue depreciado o nunca existió en el base file de clases.
3. **Buffer Flush reintroducido:** En el loop iterativo original de Facturas, permanecía escondido un `@flush()` y `@ob_flush()` originando posibles timeouts y bloqueos en las interfaces asíncronas de PHP-FPM, ignorando directrices anteriores que prohibían usar `flush()`.
4. **Ausencia de Output Operativo:** El proceso de `ingresoPedido`, curiosamente, calculaba su éxito o fracaso y loggeaba todo a los TXT escondidos del servidor, pero **no enviaba el `div` de status final al frontend**, por lo que la pantalla de Pedidos resultaba ciega para el usuario logístico.

## Causa Raíz
Copy-paste parcial entre el workflow `buscoPedido` y `buscoPedidoRXN`, heredando variables exclusivas de Facturas hacia el array parser de Pedidos y omitiendo el print del Frontend de éxito de la API al finalizar.

## Correcciones aplicadas
1. **Poda lógica de variable:** Se eliminó de `buscoPedidoRXN` el testeo y la inversión cruzada de la variable inexistente `$cant_x_precio_neto`, resolviendo en absoluto el `PHP Warning`.
2. **Reemplazo del logger fantasma:** Los tres puntos que dependían de la inexistente clase `registrarErrorLog()` fueron refactorizados a la función core de disco `error_log()`, pasándoles correctamente el input `print_r` sobre la información que no era un string (como array del artículo que arrojó el internal server error).
3. **Veto al FLUSH:** Se amputaron permanentemente del MVC los `@flush() @ob_flush()` que contaminaban y dilataban el flujo web de Facturas.
4. **Armonía operativa (Pedidos igual a Facturas):** Se copió y pegó bajo las variables del resultado HTTP del cURL API de Pedidos, el mini motor visual de HTML que devuelve al Front el ícono (✔/✘) de si ingresó bien o hubo un mensaje en la capa superior; respetando lo pautado acerca de solo visibilizar lo útil de forma limpia.

## Impacto
1. Facturas y Pedidos ya no tiran Warning técnicos.
2. Pedidos ahora sí informa el feedback transaccional hacia Tango.
3. No hay lag por buffer output manual de php.

## Validaciones esperadas
- Apretar "Procesar" y constatar que sólo salen strings con formato: `[FAC-XXXX] ✔ Factura...` o `[PID-XXXX] ✔ Pedido grabado...` o `⚠ Error API...`.

## Riesgos residuales
Los ecos que la regex detectó quedaron blindados; solo se deben considerar los `div` resultantes como output a la pantalla. Todo opera tal lo pautado.
