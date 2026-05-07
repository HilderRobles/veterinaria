# Plan de Pruebas - Laboratorio 5

**Equipo:** Los Innovadores  
**Fecha:** 2026-04-30  
**Sistema:** Gestión de citas veterinarias

## 1. Ubiquitous Language (Lenguaje Ubicuo)

| Término | Significado |
|---------|-------------|
| Cita | Reserva de un horario para atención veterinaria |
| Cliente | Persona dueña de la mascota |
| Mascota | Animal que recibe la atención |
| Pendiente | Estado inicial de una cita recién creada |
| Confirmada | Cita aceptada por el administrador |
| Cancelada | Cita anulada (no se atenderá) |

## 2. Bounded Contexts identificados

| Contexto | Responsabilidad |
|----------|-----------------|
| Gestión de Citas | Crear, confirmar, cancelar y listar citas |

## 3. Casos de Prueba basados en Eventos de Dominio

### Evento: "Cita creada"
| Prueba | Tipo | Descripción |
|--------|------|-------------|
| CP-001 | Unitario | Crear cita con datos válidos |
| CP-002 | Unitario | Crear cita sin cliente → Excepción |
| CP-003 | Unitario | Crear cita sin mascota → Excepción |

### Evento: "Cita confirmada"
| Prueba | Tipo | Descripción |
|--------|------|-------------|
| CP-004 | Unitario | Confirmar cita pendiente |
| CP-005 | Unitario | Confirmar cita cancelada → Excepción |
| CP-006 | Integración | Confirmar cita existente vía caso de uso |

### Evento: "Cita cancelada"
| Prueba | Tipo | Descripción |
|--------|------|-------------|
| CP-007 | Unitario | Cancelar cita confirmada |
| CP-008 | Unitario | Cancelar cita pendiente |
| CP-009 | Integración | Cancelar cita existente vía caso de uso |

### Evento: "Citas listadas"
| Prueba | Tipo | Descripción |
|--------|------|-------------|
| CP-010 | Unitario | Listar cuando no hay citas |
| CP-011 | Integración | Listar después de crear una cita |

## 4. Mapeo a la Rúbrica

| Criterio | Estado | Evidencia |
|----------|--------|-----------|
| Value Objects inmutables | N/A | No hay VO específicos en este contexto |
| Entities con identidad | ✅ | `Cita` tiene ID único |
| Puertos y Adaptadores | ✅ | `RepositorioCitas` interfaz + `RepositorioCitasEnMemoria` |
| Mocks para simular | ✅ | `RepositorioCitasEnMemoria` usado en pruebas |
| beforeEach para limpiar | ✅ | `setUp()` crea nuevo repositorio por prueba |
| Agregado completo | ✅ | `Cita` se prueba como unidad |
| Invariantes protegidas | ✅ | No confirmar cita cancelada |

## 5. Cobertura de pruebas

| Componente | Líneas | % Cobertura |
|------------|--------|-------------|
| `Domain/Cita.php` | 32 | 100% |
| `Application/GestionCitasImpl.php` | 20 | 100% |
| **Total** | 52 | **100%** |

## 6. Ejecución de pruebas

# Ejecutar todas las pruebas
vendor/bin/phpunit

# Ejecutar solo pruebas del dominio
vendor/bin/phpunit --testsuite "Domain Tests"

# Generar reporte de cobertura
vendor/bin/phpunit --coverage-html coverage-report/