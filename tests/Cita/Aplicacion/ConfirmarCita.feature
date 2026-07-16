# language: es
Característica: Confirmar Cita
  Como sistema quiero gestionar las transiciones de estado de las citas clínicas

  @Exitoso
  Escenario: Confirmar una cita pendiente
    Dado una cita registrada en estado "pendiente"
    Cuando la doctora confirma la atención de la cita
    Entonces la cita cambia a estado confirmada

  @ErrorEstado
  Escenario: Impedir la confirmación de una cita cancelada (INV-01)
    Dado una cita registrada en estado "cancelada"
    Cuando se intenta confirmar nuevamente la cita
    Entonces el sistema debe impedir la confirmación