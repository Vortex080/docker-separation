# Docker Separation

## INDICE
- [Docker Separation](#docker-separation)
  - [INDICE](#indice)
  - [Finalidad del proyecto](#finalidad-del-proyecto)
  - [Requisitos](#requisitos)
  - [Instalación](#instalación)
    - [Docker](#docker)
    - [Docker-compose](#docker-compose)
  - [Ejecución](#ejecución)
    - [Codigo](#codigo)
      - [Frontend](#frontend)
      - [Backend](#backend)
      - [SQL](#sql)
    - [Docker-compose](#docker-compose-1)

## Finalidad del proyecto
Tener 3 contenedores, uno para cada parte de la aplicación, que se puedan ejecutar en diferentes entornos, sin tener que instalar nada en el host.

## Requisitos
- Docker
- Docker-compose
- Cliente sql

## Instalación

### Docker
Instalar Docker en el host

### Docker-compose
Instalar Docker-compose en el host

## Ejecución

### Codigo
Preparar un repositiorio con nuestras diferentes partes del proyecto podemos hacerlo de diferentes maneras

1. Direfenciando La parte del backend y la parte del frontend en diferentes directorios
2. Usando un solo directorio y creando diferentes dockerfiles para cada parte

En mi caso voy a usar el primer metodo, ya que me parece más cómodo para manejar los distintos contenedores.

#### Frontend

Creamos nuestra carpeta de frontend y dentro de ella creamos un Dockerfile

En este caso hemos usado una imagen de php:apache, pero podemos usar cualquier imagen que nos sea útil.

El uso de WORKDIR y COPY nos permite copiar los archivos de nuestra aplicación a la imagen de php:apache

``` Dockerfile 
# Usa la imagen 
FROM php:apache

WORKDIR /var/www/html
COPY . /var/www/html 
```

En este caso solo haremos un index en el que mostraremos los datos pedidos a la base de datos a traves de la api, para ello necesitaremos los siguientes archivos:

- Una api en js

La cual tiene que conectar con el backend y pedir los datos a la base de datos. Para ello usaremos la función fetch de javascript.

```js
  let response = await fetch('http://localhost:8010/Api/UserApi.php?id=All');
  return response.json();
```

- Script.js

El script.js es el que se encarga de mostrar los datos en la tabla de usuarios.

En mi caso he creado una función que se encarga de insertar los datos en la tabla de usuarios.

```js
async function main() {
    const users = await getAll();
    // Obtener el cuerpo de la tabla
    const tbody = document.querySelector('#tablaUsuarios tbody');

    // Función para insertar filas en la tabla
    users.forEach(usuario => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
      <td>${usuario.nombre}</td>
      <td>${usuario.apellidos}</td>
      <td>${usuario.edad}</td>
    `;
        tbody.appendChild(fila);
    });
}
```

- index.html

En este archivo se encarga de mostrar la tabla de usuarios en el frontend.



#### Backend
Creamos nuestra carpeta de backend y dentro de ella creamos un Dockerfile

```Dockerfile
# Usa la imagen 
FROM php:8.2.25-cli-alpine3.20
# Instala las extensiones necesarias para MySQL/MariaDB
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html
COPY . /var/www/html 
```

Para realizar la prueba voy a añadir una api en el backend, para poder probar la conexión con la base de datos.

Para ello necesitaremos un archivo connection.php, que nos permitirá conectarnos a la base de datos.

Esta sera la función que nos permitirá conectarnos a la base de datos.

>[!NOTE]
> Revisa el archivo Connection.php para revisar las variables de la conexión

```php	
public static function getConection()
{
    if (self::$con == null) {
        self::$config = new Config();
        try {
            self::$con = new PDO(self::$config->host, self::$config->user, self::$config->pass, self::$config->options);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            exit('Esta mal :' . $e->getMessage());
        }
    }

    return self::$con;
}
```

Y un repositorio de datos, que nos permitirá manejar los datos en la base de datos.

Añadiremos tambien un UserApi.php, que nos permitirá recibir las peticiones de la api y mandarle los datos a la base de datos.

>[!IMPORTANT]
> Revisa el archivo UserApi.php se necesita darle permisos de acceso a la api en mi caso he usado los headers

```php
header('Access-Control-Allow-Origin: http://localhost:8000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

>[!NOTE]
> El metodo devuelve un json que nos permitirá trabajar con los datos en el frontend

Ademas de un archivo config para la conexión a la base de datos.

Estos son los parametros que necesitamos para poder conectarnos a la base de datos.

```php
public $host = 'mysql:host=sql;port=3306;dbname=docker';
public $user = 'root';
public $pass = 'root';
public $options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
```

#### SQL 
Para poder usar nuestra base de datos, necesitamos crear un contenedor de MySQL/MariaDB.

Que añadiremos en nuestro docker-compose

En mi caso he creado una base de datos de prueba, pero podemos usar cualquier base de datos que nos sea útil.

```sql
-- Volcando estructura de base de datos para docker
CREATE DATABASE IF NOT EXISTS `docker`
USE `docker`;

-- Volcando estructura para tabla docker.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `nombre` varchar(50) DEFAULT NULL,
  `apellidos` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla docker.usuarios: ~5 rows (aproximadamente)
INSERT INTO `usuarios` (`nombre`, `apellidos`, `edad`) VALUES
	('Juan', 'Pérez', 30),
	('Ana', 'Gómez', 25),
	('Carlos', 'Rodríguez', 35),
	('Marta', 'López', 28),
	('Luis', 'Martínez', 40);
```

### Docker-compose
El docker-compose lo crearemos en la raiz del proyecto

```bash
touch docker-compose.yml
```

Dentro de este archivo, añadiremos nuestros contenedores con los diferentes Dockerfiles

```yml
services:
  frontend:
    build:
      context: ./frontend/
      dockerfile: Dockerfile
    container_name: frontend
    depends_on:
      - backend
    networks:
      - conection
    ports:
      - "8000:80"
  backend:
    image: php:8.2.25-cli-alpine3.20
    build:
      context: ./backend/
      dockerfile: Dockerfile
    container_name: backend
    depends_on:
      - sql
    networks:
      - conection
    ports:
      - "8010:80"
  sql:
    image: mariadb:11.6.1-rc
    container_name: sql
    restart: always
    networks:
      - conection
    ports:
      - "3306:3306"
    environment:
      MARIADB_ROOT_PASSWORD: root
networks:
  conection:
    driver: bridge
```


Para ejecutar el contenedor de la aplicación, ejecutar desde el directorio raíz del proyecto:

```bash
docker-compose up --build -d
```

>[!NOTE]
> Cada cambio que se haga en cualquiera de los contenedores se necesitara reiniciar el contenedor para que tome efecto.
> Para ello se puede usar el comando que hemos visto anteriormente.


