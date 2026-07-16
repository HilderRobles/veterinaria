# language: es
Característica: Gestión de Citas Veterinaria
  La veterinaria MisPatitas necesita gestionar citas clínicas de manera confiable, evitando registros incompletos y protegiendo los estados válidos de una cita.

  @Exitoso
  Escenario: Registrar una cita válida para una mascota registrada
    Dado un cliente y una mascota registrados en la veterinaria
    Cuando la doctora agenda una cita clínica para la mascota en una fecha futura
    Entonces la cita queda registrada correctamente
    Y la cita queda en estado "pendiente"

  @ErrorFecha
  Escenario: Impedir agendar una cita en el pasado (INV-02)
    Dado un cliente y una mascota registrados en la veterinaria
    Cuando se intenta registrar una cita en una fecha del pasado
    Entonces el sistema debe impedir el registro por fecha inválida