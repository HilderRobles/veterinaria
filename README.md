## Tabla de Vinculación Arquitectónica - Laboratorio 6

| Nombre del Escenario | Puerto Primario Invocado | Técnica SWEBOK |
|----------------------|--------------------------|----------------|
| Crear una cita exitosamente | `GestionCitas::crear()` | Caja Negra |
| Intentar crear cita sin nombre de mascota | `GestionCitas::crear()` | Caja Negra |
| Confirmar una cita existente | `GestionCitas::confirmar()` | Caja Negra |
| Intentar confirmar una cita que ya fue cancelada | `GestionCitas::confirmar()` | Caja Negra |
| Cancelar una cita pendiente | `GestionCitas::cancelar()` | Caja Negra |
| Listar todas las citas de un cliente | `GestionCitas::listar()` | Caja Negra |
| Confirmar una cita pendiente exitosamente | `GestionCitas::confirmar()` | Caja Negra |
| Intentar confirmar una cita que no existe | `GestionCitas::confirmar()` | Caja Negra |
| Intentar cancelar una cita que ya está confirmada | `GestionCitas::cancelar()` | Caja Negra |
| Registrar una nueva mascota exitosamente | `GestionMascotas::registrar()` | Caja Negra |
| Intentar registrar una mascota sin nombre | `GestionMascotas::registrar()` | Caja Negra |
| Intentar registrar una mascota con especie vacía | `GestionMascotas::registrar()` | Caja Negra |

### Mapeo a la Arquitectura Hexagonal

| Escenario | Puerto Primario | Adaptador Primario | Caso de Uso |
|-----------|-----------------|-------------------|--------------|
| Gestionar citas | `GestionCitas` | `CitaController` | `GestionCitasImpl` |
| Registrar mascota | `GestionMascotas` | `MascotaController` | (Pendiente) |

### Nota sobre la técnica SWEBOK

Todos los escenarios utilizan **pruebas de caja negra** porque:
- Se basan exclusivamente en los requisitos de negocio (Given-When-Then)
- No dependen de la implementación interna
- Validan entradas y salidas sin conocer el código fuente
- Se alinean con la especificación de comportamiento (BDD)