# Cursolandia

Trabajo integrador de la facultad: una plataforma simple de cursos con foro de preguntas y respuestas.

PHP procedural + MySQL (PDO), HTML, CSS y JavaScript sin frameworks ni Composer.

## Funcionalidades

- **Home** con presentación del proyecto y planes.
- **Catálogo de cursos** con búsqueda, filtros por etiqueta y estado.
- **Detalle del curso**: datos, fechas, cupo, materiales, foro y panel lateral.
- **Registro e inicio de sesión** con sesiones y contraseñas hasheadas (`password_hash`).
- **Inscripción a cursos**:
  - gratis: queda confirmada al instante;
  - pagos: queda pendiente hasta que el docente confirma el pago;
  - controla cupo, fechas, inscripción duplicada y no permite anotarse al propio curso;
  - el plan gratuito permite hasta 3 cursos activos a la vez.
- **Crear curso** con título, descripción, etiquetas, costo, cupo, acceso y fechas.
  - El plan gratuito permite hasta 3 cursos activos (no vencidos); el Pro no tiene tope.
- **Mis cursos**: listado de los cursos creados, estado, fechas, inscriptos y preguntas.
- **Foro del curso**: los inscriptos y el docente pueden publicar preguntas; cualquiera puede leer.
- **Respuestas** en cada hilo, con la regla de no responder dos veces seguidas en el mismo hilo.

## Estructura

```
index.php            Home
cursolandia.sql      Esquema y datos de prueba
includes/
  config.example.php Plantilla de configuración
  conn.php           Conexión PDO (usa config.php)
  nav.php            Navegación común
  footer.php         Pie común
src/
  cursos.php         Catálogo
  curso.php          Detalle, inscripción y preguntas
  crear-curso.php    Alta de curso
  mis-cursos.php     Cursos del usuario
  foro-hilo.php      Hilo de preguntas y respuestas
  login.php          Inicio de sesión
  registro.php       Registro
  perfil.php         Perfil del usuario
  planes.php         Planes
styles/              Hojas de estilo
scripts/             JavaScript del front
img/                 Logos e imágenes
```

## Requisitos

- PHP 7.3 o superior con la extensión `pdo_mysql`.
- MySQL 5.7 / MariaDB 10.3 o superior.
- Apache, Nginx o el servidor embebido de PHP.

## Instalación local

1. Copiar el proyecto dentro del directorio web del servidor (por ejemplo `htdocs` en XAMPP/AppServ).
2. Crear la base de datos importando `cursolandia.sql`:

   ```bash
   mysql -u root -p < cursolandia.sql
   ```

   O importarlo desde phpMyAdmin.

3. Crear `includes/config.php` a partir de la plantilla:

   ```bash
   cp includes/config.example.php includes/config.php
   ```

   Y completar los datos reales:

   ```php
   $servername = 'localhost';
   $dbname = 'cursolandia';
   $username = 'root';
   $password = '';
   ```

   `includes/config.php` no se versiona: cada entorno usa sus propias credenciales.

4. Abrir `http://localhost/Cursolandia/` en el navegador.

## Usuarios de prueba

Todos usan la contraseña **`demo1234`**.

| Usuario | Rol   | Plan     |
|---------|-------|----------|
| admin   | admin | gratuito |
| maria   | usuario | gratuito |
| carlos  | usuario | Pro      |
| lucia   | usuario | gratuito |
| diego   | usuario | Pro      |

`maria` y `carlos` ya tienen cursos creados. `diego` tiene una inscripción pendiente en el curso 2.

## Reglas de negocio principales

- **RN01**: para crear un curso, título, descripción y fechas son obligatorios; etiquetas, costo y cupo son opcionales.
- **RN02**: el plan gratuito admite hasta 3 cursos activos (con `fecha_fin` mayor o igual a hoy). El Pro no tiene tope.
- La inscripción solo se permite dentro del período del curso y si hay cupo.
- Los cursos pagos quedan pendientes hasta la confirmación del docente.
- El plan gratuito permite estar inscripto en hasta 3 cursos activos a la vez.

## Publicación en un hosting

1. Contratar un hosting con **PHP y MySQL** (por ejemplo InfinityFree o similar).
2. Crear una base de datos desde el panel del hosting. Anotar:
   - host (`localhost` o el que indique el panel),
   - nombre de la base,
   - usuario,
   - contraseña.
3. Importar el esquema:
   - en phpMyAdmin, seleccionar la base creada e importar `cursolandia.sql`;
   - si el hosting no permite `CREATE DATABASE` ni `USE`, borrar las primeras líneas del archivo (`CREATE DATABASE ...` y `USE cursolandia;`) antes de importar.
4. Subir todos los archivos del proyecto por FTP o el administrador de archivos, manteniendo la estructura de carpetas.
5. Crear `includes/config.php` **en el servidor** con las credenciales del punto 2.
6. Verificar que la carpeta `includes/` no sea accesible públicamente desde el navegador.
7. Probar: home, registro, login, inscripción, alta de curso y foro.

## Estado del proyecto

Implementado y probado: catálogo, detalle, login, registro, inscripción, preguntas, alta de cursos y "Mis cursos".

Pendiente para futuras entregas:

- edición y baja lógica de cursos;
- subida y descarga de materiales con límites del plan gratuito;
- edición de perfil y cambio de contraseña;
- solicitud del plan Pro con comprobante y panel de administración;
- aceptar respuesta y cerrar hilo en el foro;
- notificaciones.
