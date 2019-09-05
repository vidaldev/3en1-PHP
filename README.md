# :pencil: Reto 3en1

Este repositorio pertenece a un **reto** que consiste en construir la misma aplicación en 3 lenguajes diferentes. Los lenguajes elegidos fueron los siguientes

* Python [ [repositorio](https://github.com/vidaldev/3en1-Python) | [live](https://repl.it/@vidaldev/3en1-Python) ]
* NodeJS [ [repositorio](https://github.com/vidaldev/3en1-NodeJs) | [live](https://repl.it/@vidaldev/3en1-NodeJs) ]
* PHP [ [repositorio](https://github.com/vidaldev/3en1-PHP) ]

>La única regla es que el flujo de tareas y navegación que siguen los usuarios para completar las tareas sea el mismo en los 3 lenguajes. Puedes elegir los que más te gusten. Puedes seguir diferentes paradigmas, principios y buenas prácticas de programación. Pero la aplicación debe verse absolutamente igual en los 3 proyectos.

Link del reto [aqui](https://platzi.com/blog/platziretos-3-languages-challenge/)

## :trophy: Puesto clasificatorio

**3er lugar** :medal_military: ([resultados aquí](https://github.com/juandc/3-languages-challenge))

## 🚀 Comenzando

Tema principal es un **API REST CRUD** sobre alquiler de vehículos, todos los proyectos apuntan a una base de datos en **firebase**, tiene sistema a **AUTH**. En la introducción de este documento se ha explicado donde encontrar cada proyecto y donde puedes ver el proyecto funcionando perfectamente.

En el caso de **PHP** explicaremos como hacer que funcione en nuestra local.

### 📋 Pre-requisitos

Para este proyecto se usaron las siguientes versiones con los siguientes modulos/plugins:

![composer](https://img.shields.io/badge/_Composer_-1.6.3-blue?style=for-the-badge)

![php](https://img.shields.io/badge/PHP_version-7.2.19-blue?style=for-the-badge)

![ubuntu](https://img.shields.io/badge/Ubuntu_LTS-18.04.3-blue?style=for-the-badge)

* php7.2-curl
* php7.2-xml
* php7.2-grpc
* php7.2-mbstring

Espero no estar olvidando alguno, de ser así no te preocupes, al momento de la instalación te solicitará instalarlo.

Para saber la versión de tu php:

```bash
~$ php -v

PHP 7.2.19-0ubuntu0.18.04.2 (cli) (built: Aug 12 2019 19:34:28) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Xdebug v2.7.2, Copyright (c) 2002-2019, by Derick Rethans
    with Zend OPcache v7.2.19-0ubuntu0.18.04.2, Copyright (c) 1999-2018, by Zend Technologies
```

Para saber la version de tu linux:

```bash
~$ lsb_release -a

No LSB modules are available.
Distributor ID: Ubuntu
Description: Ubuntu 18.04.3 LTS
Release: 18.04
Codename: bionic
```

Para saber que modulos de php ya tienes disponibles:

```bash
~$ php -m

[PHP Modules]
...
...
...
```

Para saber la version del composer:

```bash
~$ composer -v

   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer 1.6.3 2018-01-31 16:28:17
```

### 🔧 Instalación

Localizamos el directorio donde deseamos bajar este repositorio y ejecutamos lo siguiente:

```bash
~$ git clone git@github.com:vidaldev/3en1-PHP.git
```

Puedes hacer un fork en caso de tu poseer una cuenta github (acepto mejoras de código). Luego de esto ingresa a la carpeta:

```bash
~$ cd 3en1-PHP
/3en1-PHP ~$ composer install
```

Si todo marcha bien las siguientes líneas serán en verde y estaremos listos para usar el proyecto

### ⚠️Importante

Para este proyecto, una vez hecho los pasos anteriores, es necesario activar el gRPC para que no de ningun error las librerías relacionadas a **Firebase**. Lo haremos de la siguiente manera:

* Instalamos el **PECL**

```bash
~$ sudo apt-get install autoconf libz-dev php7.2-dev php-pear
```

* Instalamos la extensión **gRPC**

```bash
~$ sudo pecl install grpc
```

* Editamos el archivo `php.ini` (Su ruta normalmente es /etc/php/7.2/cli/php.ini) y agregamos la siguiente línea:

> extension=grpc.so

* Ya en el composer.json se encuentra requerido

### 🛠️ Pruebas

Para correr las pruebas basta con situarse en el directorio del proyecto y ejecutar

```bash
~$ php -S localhost:8080
```

El servidor de prueba estaría corriendo en el puerto `8080`

### ⚙️ Uso / metodos / parametros

Para todos los request de manera obligatoria deben ir el correo y la contraseña

|               DESCRIPCION               |        URL       | METODO |                                             PARAMETROS                                            |
|:---------------------------------------:|:----------------:|:------:|:-------------------------------------------------------------------------------------------------:|
| Comprobar usuario                       | /login           |   GET  | email, password                                                                                   |
| Crear usuario                           | /createUser      |  POST  | email, password                                                                                   |
| Recuperar Clave                         |  /forgotPassword |  POST  | email                                                                                             |
| Abrir un alquiler                       | /alquilar        |  POST  | email, password, modelo, marca, year, color, responsable                                          |
| Cerrar un alquiler                      | /cerrarAlquiler  |  POST  | email, password, id (Del alquiler abierto), filtro (entregado)                                    |
| Corregir datos del alquiler             | /corregirDatos   |  POST  | email, password, id (Del alquiler), parametros a corregir (modelo, marca, year,color, responsable) |
| Listar todos los alquileres             | /alquileres      |  POST  | email, password, filtro (entregado, pendiente o todo)                                             |
| Listar todos los alquileres del usuario | /alquileres/user |  POST  | email, password, filtro (entregado, pendiente o todo)                                             |

### ✔️ Para recordar

No olvides configurar el archivo `.env` y descargar el `ServiceAccountKey.json` de tu cuenta **firebase**

---
