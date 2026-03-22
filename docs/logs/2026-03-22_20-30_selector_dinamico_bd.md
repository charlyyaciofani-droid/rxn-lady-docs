# [Configuraciones] — [Iteración 13 - Selector Dinámico de Base de Datos]

## Contexto
El campo "Base de datos" dentro del panel de *Configuraciones* venía funcionado como un Input manual clásico (`type="text"`). Esto, si bien era 100% operativo, habilitaba un riesgo implícito de error humano: errores de tipeo al momento de definir la base de datos de trabajo del sistema, lo cual arrojaba caídas en la integración con Tango.  

Se encargó iterar el módulo para proveer una UI más segura e inteligente, instanciando los listados directamente de la tabla central de la instalación sin ensuciar la lógica base del legacy ni importar librerías pesadas / frameworks innecesarios.

## Problema
- El usuario debía tipear el nombre de la BD objetivo (ej: `LADY_WAY_SRL_AL_...`) a tientas, sin feedback.
- El riesgo de ingresar un entorno inexistente en un input crudo es elevado en sistemas logísticos.

## Análisis e Ingeniería
El requerimiento prohibía explícitamente el uso de frameworks o refactors profundos, demandando el aprovechamiento in situ del esquema general actual. La opción ganadora recayó en el componente HTML5 `<datalist>` por ser:
- Nativo.
- Increíblemente liviano e invisible a la inyección estética de `rxn-ui.css`.
- Compatible retrospectivamente: si el datalist falla silenciosamente o el navegador no lo soporta, degrada elegantemente a un `<input type="text">` común y silvestre.

## Implementación
1. **Conector Lateral (`configuraciones/modelo.php`)**  
   Se creó el método `traerBasesDisponibles()`. Como `modelo.php` no poseía instanciación a la DB principal de configuración sino a la DB de origen, se inyectó el llamado al script universal `Conectar.php` dentro del mismo scope de la función. Esto aisló la dependencia, llamó a `Conectar_SQL::conexion()` apuntanda a `DiccionarioCharly` y extrajo `$fila['NombreBD']` mediante un clausurado iterativo de `DISTINCT`. Un atrapador de Excepciones (`try...catch`) prevé devolver un Array vacío como fallback seguro en caso de corte de red, evitando matar la visual general de la UI.
2. **Inyección en la Vista (`configuraciones/index.php`)**  
   Al `<input>` correspondiente en la tabla gráfica se le adhirió el atributo `list="dl-bases"` y `autocomplete="off"`. A continuación se incrustó el tag `<datalist>` iterando con un `foreach` la devolución de `traerBasesDisponibles()`, empujando opciones en HTML que el navegador autoindiza por default en un Dropdown auto-completador de texto a medida de la búsqueda.

## Impacto
- UX mejorada y blindada: el usuario ahora redacta la primera sigla de la empresa y selecciona del panel desplegable con auto-completado en tiempo real.
- Compatibilidad impecable: el valor viaja íntegro bajo la variable POST `Nombre_base` que requiere el método salvador en PHP, inalterado.
- Re-uso total del esquema actual de DB `DiccionarioCharly`.

## Riesgos residuales / Fallbacks
- El sistema ha quedado diseñado para que si el `DiccionarioCharly` se apaga temporalmente —por reinicio del SQL server— el componente no colapse la pantalla web: el Datalist sencillamente no se expone y el Frontend tolera el ingreso manual tal cual funcionaba en la iteración pasada.
- **Riesgo Visual:** Nulo, el datalist hereda incondicionalmente la tipografía y padding asignados a todos los Text Inputs de `rxn-ui.css`.

## Validación
- La base de datos sugerida se nutre estrictamente de `Empresa.NombreBD`.
- No hay repetidos gracias a `SELECT DISTINCT`.
- Filtran vacíos con `IS NOT NULL AND LTRIM(RTRIM(NombreBD)) <> ''`.
