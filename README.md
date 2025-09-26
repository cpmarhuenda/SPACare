## Plataforma de Gestión Psicológica Online

El siguiente proyecto es una plataforma web desarrollada en **Laravel 11** con **MoonShine 2.0** que facilita la gestión de citas médicas online entre psicólogos, pacientes y personal administrativo.  
El sistema integra funcionalidades de **videollamada**, **asignación de recursos**, **historia clínica**, **mensajería interna** y un **calendario interactivo** para mejorar la atención psicológica en entornos digitales.

---

## Características principales

- **Gestión de usuarios**: Pacientes, Psicólogos, Administrativos y Super Admin.
- **Citas online**: creación, modificación, calendario y acceso a videollamadas (Jitsi Meet).
- **Recursos**: subida y asignación de documentos (PDF, Word, TXT).
- **Historia clínica**: registro y seguimiento clínico de pacientes.
- **Mensajería interna**: comunicación estructurada entre usuarios.
- **Notificaciones**: avisos automáticos al asignar recursos o citas.
- **Panel MoonShine** adaptado al estilo corporativo de la UNED (verde corporativo) pero modificable gracias al archivo css.


## Requisitos del sistema

Antes de instalar el proyecto, asegúrate de tener:

- **PHP** >= 8.2  
- **Composer** >= 2.5  
- **MySQL** >= 8.0 (ó MariaDB 10.6+)  
- **Node.js** >= 18 + **npm**  
- **Servidor web**: Apache / Nginx  
- **Extensiones PHP**: `mbstring`, `pdo`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
  
## Instalación
Una vez descargado el contenido del repositorio e instalado el software indicado en el apartado anterior, realizar las siguientes acciones:

### 1. Clonar el repositorio
  bash
   git clone https://github.com/tuusuario/spacare.git
   cd spacare

### 2. Instalar dependencias de PHP
    composer install

### 3. Instalar dependencias de Node.js
    npm install

### 4.Crear base de datos
    php artisan db:create

### 5. Configurar el entorno
    cp .env.example .env
    php artisan key:generate

#### Configura tu base de datos en .env:
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=spacare
        DB_USERNAME=usuario
        DB_PASSWORD=contraseña

### 6. Ejecutar migraciones y seeders:
     php artisan migrate --seed

### 7. Compilar los assets
    npm run build
    
### 8. Levantar el servidor de desarrollo
    php artisan serve
    El proyecto estará disponible en http://localhost:8000

### 9. Crear usuario inicial Super Admin
    php artisan make:superadmin tuemail tucontraseña


## Roles y accesos

- **Administrativo:** Crea pacientes, psicólogos, citas y recursos.
- **Psicólogo:** Asigna citas y recursos, consulta la historia clínica y realiza seguimiento.
- **Paciente:** Accede a sus citas, recursos asignados y mensajes.
- **Super Admin:** Control total del sistema.


## Personalización del entorno:

### Estilos
El proyecto incluye archivos de personalización en:
- **resources/css/custom.css:** Colores, estilos y adaptaciones al diseño UNED.  
- **resources/js/custom.js:** Ajustes de comportamiento del panel (menú móvil, etc.).  

Para recompilar los assets tras modificar estos archivos:
    npm run dev   # en desarrollo
    npm run build # en producción

### Imágenes y logos
Tus imágenes de **logo, favicon, iconos de botones** las tienes en `public/` 
    (ejemplo `public/images/logo.png`, `public/favicon.ico`, etc.).  
    Como están en `public`, se sirven directamente desde la web.  

### Recursos gráficos
Los iconos y logos se encuentran en `public/` debes ubicarlos ahi para poder mostrarlos en la         aplicación:
- **public/images/Logo_SPACare_H.png:** Logo principal.
- **public/images/favicon-32x32.png:** Favicon.  
- **public/icons/:** Iconos de botones (nuevo, editar, cancelar, etc.).  


## Licencia
Este proyecto ha sido desarrollado como parte del Trabajo de Fin de Grado (TFG) en Ingeniería Informática – UNED.
Puedes usarlo con fines académicos y educativos.
