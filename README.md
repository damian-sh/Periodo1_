# Monorepo — Guías PHP (KARLA)

Los 3 trabajos en una sola carpeta. Requiere PHP 8.1+ y Composer.

## 1. `ejercicio1/` — Sistema de Inventario

```bash
cd ejercicio1
composer install        # o: composer dump-autoload
php public/index.php
```

Clases: `App\Producto`, `App\Inventario` (arreglo de objetos + acumulador).

## 2. `ejercicio2/` — Sistema de Biblioteca

```bash
cd ejercicio2
composer install        # o: composer dump-autoload
php public/index.php
```

Clases: `App\Autor`, `App\Libro`, `App\Biblioteca` (composición de objetos + búsqueda).

## 3. `practica2/` — BiblioTech (Práctica Evaluada II)

```bash
php practica2/biblioteca.php
```

Un solo archivo, sin Composer. Clase abstracta `Material`, hijas `Libro` y
`Revista`, multas con `match(true)` y conceptos POO marcados con comentarios
(`[VARIABLE]`, `[ARREGLO]`, `[CLASE]`, `[OBJETO]`, `[ABSTRACCIÓN]`,
`[ENCAPSULAMIENTO]`, `[HERENCIA]`, `[POLIMORFISMO]`).

## 4. `siga-uni/` — SIGA-UNI v2 (persistencia + reportes)

```bash
cd siga-uni
composer install --ignore-platform-req=ext-gd --ignore-platform-req=ext-iconv
php main.php
```

> Las extensiones `gd` e `iconv` se ignoran porque los reportes no usan imágenes.
> Si tu equipo las tiene instaladas, usa solo `composer install`.

Al ejecutar:

1. Si existe `data/participantes.json`, ofrece cargarlo (S/n) en vez de recrear datos.
2. Imprime el reporte de consola del ciclo.
3. Genera:
   - `data/participantes.json`
   - `reportes/reporte.txt`
   - `reportes/reporte.csv`
   - `reportes/reporte.xlsx` (hojas "Participantes" y "Resumen", encabezados en negrita)
   - `reportes/reporte.pdf` (encabezado institucional, ciclo y fecha)

Para recrear los datos desde cero aunque exista el JSON, responde `n` al cargar.

### Diseño (requisitos RT-31 a RT-45)

- `RepositorioParticipantes`: encapsula lectura/escritura JSON con excepciones.
- Interfaz `ExportadorReporte` implementada por `ExportadorTexto`, `ExportadorCSV`,
  `ExportadorExcel` y `ExportadorPDF`.
- `ReporteCiclo` recibe el exportador por inyección de dependencias y no contiene
  ningún `if`/`switch` por formato (polimorfismo, patrón Strategy).
- CSV con `fopen()`+`fputcsv()` y verificación de lectura con `SplFileObject`.
