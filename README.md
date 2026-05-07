## Tabla de Vinculación Arquitectónica - Laboratorio 6

| Nombre del Escenario | Puerto Primario Invocado | Técnica SWEBOK Aplicada |
|----------------------|--------------------------|-------------------------|
| Crear una cita exitosamente | `GestionCitas::crear()` | Caja Negra (Black Box) |
| Intentar crear cita sin nombre de mascota | `GestionCitas::crear()` | Caja Negra (Black Box) |
| Confirmar una cita existente | `GestionCitas::confirmar()` | Caja Negra (Black Box) |
| Intentar confirmar cita cancelada | `GestionCitas::confirmar()` | Caja Negra (Black Box) |
| Cancelar una cita pendiente | `GestionCitas::cancelar()` | Caja Negra (Black Box) |
| Listar todas las citas de un cliente | `GestionCitas::listar()` | Caja Negra (Black Box) |
| Registrar una nueva mascota exitosamente | `GestionMascotas::registrar()` | Caja Negra (Black Box) |
| Intentar registrar mascota sin nombre | `GestionMascotas::registrar()` | Caja Negra (Black Box) |

### Mapeo a la Arquitectura Hexagonal

| Escenario | Puerto Primario | Adaptador Primario | Caso de Uso |
|-----------|-----------------|-------------------|--------------|
| Gestionar citas | `GestionCitas` | `CitaController` | `GestionCitasImpl` |
| Registrar mascota | `GestionMascotas` | `MascotaController` | `RegistrarMascota` |

### Nota sobre la técnica SWEBOK

Todos los escenarios utilizan **pruebas de caja negra** porque:
- Se basan exclusivamente en los requisitos de negocio (Given-When-Then)
- No dependen de la implementación interna
- Validan entradas y salidas sin conocer el código fuente
- Se alinean con la especificación de comportamiento (BDD)