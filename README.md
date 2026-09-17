# Gestor Documental Jurídico (SGDJ)

**Proyecto académico SENA** — Gestión documental jurídica.

## 1. Nombre provisional del proyecto

**Gestor Documental Jurídico** — sigla provisional **SGDJ**.

> El nombre es provisional y puede ajustarse en fases posteriores.

## 2. Descripción del proyecto

Aplicación web de gestión documental jurídica cuyo propósito es
organizar y administrar información relacionada con usuarios, clientes,
procesos jurídicos, documentos, tipos o categorías documentales, así
como facilitar la búsqueda y los filtros de dicha información y la
administración general del sistema.

## 3. Objetivo general

Construir una aplicación web de gestión documental jurídica que permita
organizar, consultar y administrar la información de manera segura,
ordenada y mantenible, utilizando tecnologías web estándar.

## 4. Tecnologías utilizadas

| Tecnología      | Uso                                              |
|-----------------|--------------------------------------------------|
| HTML5           | Estructura de la interfaz (semántico).           |
| CSS3            | Estilos y diseño responsivo.                     |
| JavaScript      | Interactividad (JavaScript vanilla, sin frameworks). |
| PHP 8+          | Lógica de la aplicación (sintaxis moderna).      |
| MySQL Server    | Motor de base de datos.                          |
| MySQL Workbench | Modelado y administración de la base de datos (herramienta, no motor). |
| PDO             | Acceso de PHP a MySQL con consultas preparadas.  |
| XAMPP / Laragon | Entorno local de desarrollo (PHP y MySQL).       |

**Nota de la Fase 1:** no se utiliza ninguna dependencia externa,
framework ni librería (sin Composer, sin `vendor/`).

## 5. Arquitectura inicial

La arquitectura separa los archivos públicos (document root) de la lógica
y configuración interna, de modo que el navegador únicamente accede a
`public/`. El resto de carpetas queda protegida con `.htaccess`
(`Require all denied`).

- `public/` es el **document root**: solo aquí viven los archivos accesibles por el navegador.
- `config/` contiene configuración y la conexión PDO; no es accesible públicamente.
- `app/` contiene la lógica PHP futura (controladores, modelos, vistas, módulos y ayudas).
- `database/` reserva el espacio para scripts SQL y el modelo de MySQL Workbench (Fase 2).

## 6. Estructura de carpetas

```
gestor-documental-juridico/
├── .gitignore
├── .htaccess
├── README.md
├── config/                          # Configuración (protegida)
│   ├── .htaccess
│   ├── app.php                      # Constantes generales de la aplicación
│   ├── database.example.php         # Plantilla de credenciales (se sube a git)
│   ├── database.php                 # Credenciales locales (EXCLUIDA de git)
│   └── conexion.php                 # Clase Conexion (PDO, patrón singleton)
├── app/                             # Lógica PHP (protegida)
│   ├── .htaccess
│   ├── controllers/                 # Reservado: controladores (Fase 2+)
│   ├── models/                      # Reservado: modelos de datos (Fase 2+)
│   ├── views/                       # Reservado: vistas / interfaz (Fase 2+)
│   ├── modulos/                     # Reservado: módulos del sistema (Fase 2+)
│   └── helpers/
│       ├── .htaccess
│       └── functions.php            # Funciones auxiliares (esc, normalizar)
├── database/                        # Base de datos (protegida)
│   ├── .htaccess
│   ├── scripts/                     # Reservado: scripts SQL (Fase 2)
│   └── modelo/                      # Reservado: modelo MySQL Workbench (Fase 2)
└── public/                          # Document root (public)
    ├── index.php                    # Página inicial de verificación
    ├── css/
    │   └── styles.css               # Hoja de estilos principal
    ├── js/
    │   └── main.js                  # JavaScript principal
    └── assets/
        ├── img/                     # Imágenes (reservado)
        └── fonts/                   # Tipografías (reservado)
```

### Explicación de carpetas principales

- **`public/`** — Única carpeta expuesta al navegador. Contiene la página inicial,
  CSS, JavaScript y recursos estáticos.
- **`config/`** — Configuración de la aplicación y de la base de datos, más la
  clase de conexión PDO. Separada del código de la aplicación.
- **`app/`** — Lógica PHP de la aplicación. Los subdirectorios `controllers`,
  `models`, `views` y `modulos` quedan preparados (vacíos) para fases posteriores.
- **`database/`** — Espacio reservado para scripts SQL y para el modelo
  entidad-relación creado con MySQL Workbench.
- **`app/helpers/functions.php`** — Funciones auxiliares comunes reutilizables.

## 7. Requisitos para ejecutar el proyecto

- PHP **8.0 o superior** (con extensiones `PDO` y `pdo_mysql`).
- MySQL Server (incluido en XAMPP o Laragon).
- MySQL Workbench (para modelado y administración; puede instalarse aparte).
- Un entorno local tipo **XAMPP** o **Laragon** (Apache + PHP + MySQL).
- Un navegador web actualizado.

## 8. Instalación de PHP/MySQL mediante el entorno local

**Opción A — XAMPP**

1. Descargar e instalar XAMPP desde <https://www.apachefriends.org/>.
2. Desde el **XAMPP Control Panel** iniciar los servicios **Apache** y **MySQL**.
3. Copiar este proyecto en `C:\xampp\htdocs\gestor-documental-juridico`.

**Opción B — Laragon**

1. Descargar e instalar Laragon desde <https://laragon.org/>.
2. Iniciar Laragon y pulsar **Start All** (Apache + MySQL).
3. Copiar este proyecto en la carpeta `www` de Laragon
   (normalmente `C:\laragon\www\gestor-documental-juridico`).

## 9. Configuración inicial

1. Copiar `config/database.example.php` como `config/database.php`.
2. Ajustar los valores de conexión al entorno local:
   - `DB_HOST`: `127.0.0.1` y `DB_PORT`: `3306` por defecto.
   - `DB_NAME`: nombre de la base de datos (se definirá en la Fase 2).
   - `DB_USER` y `DB_PASSWORD`: credenciales de MySQL del equipo.
3. El archivo `config/database.php` ya está excluido por `.gitignore`
   para que las credenciales nunca se suban al repositorio.

> No se utilizan credenciales reales en esta fase; los valores de
> `database.php` son locales y de ejemplo.

## 10. Uso de MySQL Server

MySQL Server es el motor de base de datos. En esta fase solo es necesario
tener el servicio iniciado (desde XAMPP Control Panel o Laragon). La
base de datos **no se crea todavía**: su diseño forma parte de la Fase 2.

Puede verificarse el estado de MySQL con:

```
mysql --version
```

## 11. Uso de MySQL Workbench

MySQL Workbench es la herramienta de administración y **modelado**, no el
motor de base de datos. En la Fase 2 se utilizará para:

- Crear el modelo entidad-relación (MER).
- Diseñar y crear tablas.
- Definir claves primarias (PK) y claves foráneas (FK).
- Establecer relaciones entre tablas.
- Ejecutar scripts SQL.
- Administrar la base de datos del sistema.

Los artefactos generados (archivo `.mwb` y scripts `.sql`) se guardarán en
`database/modelo/` y `database/scripts/`, respectivamente.

## 12. Cómo se conectará PHP con MySQL mediante PDO

La conexión está preparada en `config/conexion.php` mediante la clase
`Sgdj\Database\Conexion`, que expone un único método `obtener()`:

```php
use Sgdj\Database\Conexion;

$pdo = Conexion::obtener();
```

Características de la conexión preparada:

- **Consultas preparadas reales**: `PDO::ATTR_EMULATE_PREPARES => false`
  (mayor seguridad frente a inyección SQL).
- **Errores controlados**: `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
  (manejo mediante excepciones).
- **Resultados como arreglos asociativos**: `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`.
- **Juego de caracteres UTF-8**: `utf8mb4` en el DSN.

Las credenciales se leen desde `config/database.php` (archivo local y
excluido del repositorio) y nunca aparecen en el código de la aplicación.
Los detalles técnicos de error solo se muestran en entorno `development`.

## 13. Cómo ejecutar el proyecto localmente

La carpeta que debe servir el servidor web es `public/`, porque es el
document root del proyecto.

**Forma A — Carpeta pública como raíz (recomendada)**

Crear un virtual host en Apache que apunte a `public/`:
`localhost` servirá directamente la aplicación.

**Forma B — Acceso directo desde htdocs / www**

Copiando el proyecto en `htdocs` (XAMPP) o `www` (Laragon), abrir en el
navegador:

```
http://localhost/gestor-documental-juridico/public/
```

**Forma C — Servidor de desarrollo de PHP (rápida)**

```bash
php -S localhost:8000 -t public
```

Luego abrir `http://localhost:8000/`.

La página inicial muestra el estado de la configuración: requisitos del
entorno (PHP, PDO, controlador MySQL) y el estado de la conexión a la base
de datos (su creación corresponde a la Fase 2).

## 14. Qué fue construido en la Fase 1

- Estructura de carpetas profesional y separación de responsabilidades.
- `config/app.php`: configuración general de la aplicación.
- `config/database.example.php` + `config/database.php`: configuración de
  MySQL separada de la aplicación y excluida mediante `.gitignore`.
- `config/conexion.php`: clase `Conexion` (PDO) preparada para MySQL con
  consultas preparadas reales y manejo controlado de errores.
- `app/helpers/functions.php`: funciones auxiliares (`esc`, `normalizar`).
- `public/index.php`: página inicial mínima con indicador del estado de
  configuración.
- `public/css/styles.css` y `public/js/main.js`: recursos base.
- Protección de carpetas sensibles con `.htaccess` (`Require all denied`).
- `README.md`: documentación del proyecto.

## 15. Qué NO fue construido (fases posteriores)

Deliberadamente **no** se implementó en esta fase:

- Diseño ni creación de la base de datos, tablas o relaciones definitivas.
- CRUD, login, registro de usuarios, dashboard definitivo.
- Gestión de documentos, clientes o procesos jurídicos.
- Tipos documentales, búsqueda y filtros.
- Permisos, reportes ni funcionalidades avanzadas.
- Autenticación, autorización, roles ni sesiones.
- Configuraciones de producción.

> La Fase 1 queda pendiente de revisión y aprobación antes de continuar
> con la Fase 2 (diseño de la base de datos con MySQL Workbench).