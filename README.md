Markdown


# 🐾 Sistema de Gestión Veterinaria

[![Hito 1 - Calidad CI/CD](https://github.com/tu-usuario/veterinaria/actions/workflows/calidad.yml/badge.svg)](https://github.com/tu-usuario/veterinaria/actions)
[![PHP Version](https://img.shields.io/badge/php-%E2%89%A5_8.5.6-8892BF.svg?style=flat-square)](https://php.net)
[![Mutation Score Indicator](https://img.shields.io/badge/MSI-100%25-brightgreen.svg?style=flat-square)](https://infection.github.io)

Este proyecto representa el núcleo core de un sistema de gestión de clínicas veterinarias, desarrollado bajo los más estrictos estándares de **Arquitectura Hexagonal**, **Domain-Driven Design (DDD)** y **Testing de Alta Confianza**. 

El módulo actual de `Cliente` cuenta con un blindaje garantizado del **100% en Cobertura de Código (Code Coverage)** y **100% en Indicador de Puntuación de Mutación (MSI)**, impidiendo regresiones tácticas o fallos lógicos en el negocio.

---

## 🛠️ Requisitos Previos del Sistema

Antes de clonar e inicializar el proyecto en tu máquina local, asegúrate de cumplir con las siguientes dependencias de entorno:

* **Entorno de Ejecución:** PHP v8.5.6 o superior (con soporte CLI habilitado).
* **Gestor de Paquetes:** [Composer v2.x](https://getcomposer.org/) configurado de forma global.
* **Motor de Cobertura:** [Xdebug v3.x](https://xdebug.org/) instalado y activo para el entorno de consola (`php.ini`).
  * ⚠️ *Importante:* La directiva `xdebug.mode` debe incluir el valor `coverage` en tu configuración de PHP local para permitir la recolección de métricas.

---

## 🚀 Clonación, Instalación y Configuración Local

Ejecuta los siguientes comandos en tu terminal de preferencia para configurar el entorno de desarrollo desde cero:

### 1. Clonar el repositorio de forma limpia
```bash
git clone [https://github.com/tu-usuario/veterinaria.git](https://github.com/tu-usuario/veterinaria.git)
cd veterinaria
2. Instalar dependencias del proyecto (Ecosistema de Desarrollo)
Este comando descarga e inicializa los motores de prueba de caja blanca, caja negra y mutación (phpunit, infection), optimizando el mapa de clases:

Bash


composer install
💡 Nota de Ingeniería: La carpeta vendor/ está estrictamente excluida en el archivo .gitignore. Composer se encarga de compilarla localmente de forma nativa para tu arquitectura y sistema operativo. El pipeline de CI/CD repetirá este proceso de forma aislada en la nube.

3. Configurar variables de entorno y secretos
Modifica y duplica la plantilla base para inicializar tus variables locales (conexión a bases de datos en Wamp64/MySQL, claves de sandbox, etc.):

Bash


cp .env.example .env
🧪 Ejecución de la Suite de Pruebas
Las pruebas automatizadas están segregadas en suites independientes dentro de phpunit.xml para asegurar ejecuciones quirúrgicas y veloces durante el ciclo de vida del desarrollo.

1. Pruebas Unitarias (Lógica Pura de Dominio y Aplicación)
Validan el comportamiento de tus Casos de Uso, Entidades y Objetos de Valor en aislamiento total. No tocan bases de datos ni servicios de red externos utilizando Mocks estandarizados.

Bash


vendor/bin/phpunit --testsuite "Domain Tests"
2. Pruebas de Integración (Frontera de Infraestructura)
Validan la persistencia real, adaptadores de bases de datos, inyección de dependencias y la Capa Anticorrupción (ACL) contra agentes externos simulados.

Bash


vendor/bin/phpunit --testsuite "Integration Tests"
3. Ejecución Completa (Suite Global)
Si deseas correr absolutamente todos los tests de forma concurrente, ejecuta PHPUnit de manera directa:

Bash


vendor/bin/phpunit
💥 Pruebas de Mutación Avanzadas (Infection PHP)
Para garantizar que tus pruebas unitarias son verdaderamente resistentes y capaces de interceptar errores de lógica humana en el futuro, implementamos Infection. Este altera operadores matemáticos, condicionales y valores de retorno en tiempo de ejecución para validar si tus tests actuales detectan ("matan") las mutaciones.

Paso obligatorio: Generar el mapa de cobertura previo con Xdebug
Infection no puede mutar a ciegas; requiere un índice de cobertura en formato XML generado de manera exacta por PHPUnit:

Bash


vendor/bin/phpunit --log-junit=build/phpunit.junit.xml --coverage-xml=build/coverage-xml
Paso final: Lanzar las mutaciones enfocadas en el módulo de Cliente
Utilizamos hilos en paralelo (--threads=max) y restringimos el análisis exclusivamente a las capas puras de Cliente para optimizar el tiempo de respuesta y el consumo de CPU:

Bash


vendor/bin/infection --coverage=build --threads=max --filter=src/Cliente/Dominio,src/Cliente/Aplicacion --ansi
🤖 Integración Continua (CI/CD Quality Gate)
Este repositorio implementa un flujo automatizado de integración continua mediante GitHub Actions localizado en .github/workflows/calidad.yml. Se ejecuta automáticamente ante cualquier evento de push en main o en ramas feature/*, así como en cada pull_request.

Filtros de Control y Políticas de Falla (Quality Gate)
El pipeline asegura la calidad mediante tres fases restrictivas:

PHP Lint: Escaneo estático en busca de errores de sintaxis en el 100% de los archivos PHP del proyecto.

Execution Test: Corridas automáticas de "Domain Tests" e "Integration Tests" en un contenedor aislado con Linux Ubuntu, aprovisionando dinámicamente PHP con Xdebug en su misma versión local.

Infection Gate Extremo: El pipeline marcará un error crítico de integración ❌ y bloqueará el botón de Merge si el código enviado reduce el MSI Global por debajo del 70% o el Covered Code MSI por debajo del 90%.

Los reportes HTML y logs detallados generados en la nube se suben automáticamente como artefactos descargables (reporte-mutacion-global) con una retención estricta de 5 días.

📂 Arquitectura de Carpetas y Módulos
El proyecto sigue una distribución modular limpia orientada a desacoplar el core del negocio de los frameworks y detalles de infraestructura:

Plaintext


.
├── .github/workflows/     # Configuración del pipeline de CI/CD (GitHub Actions)
├── src/                   # Código de producción de la aplicación
│   └── Cliente/           # Módulo enfocado en Clientes / Propietarios
│       ├── Dominio/       # Reglas de negocio puras, Entidades y Objetos de Valor (Inmunes a cambios externos)
│       ├── Aplicacion/    # Casos de Uso del sistema, Puertos e Interfaces conceptuales
│       └── Infraestructura/# Adaptadores reales, Persistencia DB, Logs y Capa Anticorrupción (ACL)
├── tests/                 # Estructura espejo exacta para el blindaje de pruebas
│   └── Cliente/           # Suite completa de pruebas unitarias e integración del módulo
├── build/                 # Directorio local temporal de reportes de cobertura (Excluido de Git)
├── phpunit.xml            # Configuración estricta de suites y mapa de cobertura para PHPUnit
└── infection.json5        # Parámetros avanzados y mutadores excluidos del sistema de mutación