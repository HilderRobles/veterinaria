# Feature: Gestión de Citas
# Como cliente de la clínica veterinaria
# Quiero gestionar citas para mi mascota
# Para poder llevar a mi mascota al veterinario

Feature: Gestion de Citas

  Background:
    Given el sistema está funcionando correctamente
    And existe un cliente registrado con nombre "Ana Lopez"

  # ==========================================
  # ESCENARIO 1: Camino feliz - Crear una cita
  # ==========================================
  Scenario: Crear una cita exitosamente
    Given la mascota "Luna" pertenece al cliente "Ana Lopez"
    When el cliente solicita una cita para la mascota "Luna" 
      en la fecha "2025-02-20" a las "10:00:00"
    Then el sistema registra la cita con estado "pendiente"
    And el sistema asigna un identificador único a la cita
    And el sistema notifica al cliente que la cita fue creada

  # ==========================================
  # ESCENARIO 2: Ruta de error - Datos inválidos
  # ==========================================
  Scenario: Intentar crear una cita sin nombre de mascota
    Given la mascota "" pertenece al cliente "Ana Lopez"
    When el cliente solicita una cita para la mascota "" 
      en la fecha "2025-02-20" a las "10:00:00"
    Then el sistema rechaza la solicitud
    And el sistema muestra el mensaje "Mascota requerida"

  # ==========================================
  # ESCENARIO 3: Confirmar cita
  # ==========================================
  Scenario: Confirmar una cita existente
    Given existe una cita pendiente con identificador "1"
    When el administrador confirma la cita con identificador "1"
    Then el sistema cambia el estado de la cita a "confirmada"
    And el sistema notifica al cliente que su cita fue confirmada

  # ==========================================
  # ESCENARIO 4: Ruta de error - Confirmar cita cancelada
  # ==========================================
  Scenario: Intentar confirmar una cita que ya fue cancelada
    Given existe una cita cancelada con identificador "2"
    When el administrador intenta confirmar la cita con identificador "2"
    Then el sistema rechaza la operación
    And el sistema muestra el mensaje "No se puede confirmar cita cancelada"

  # ==========================================
  # ESCENARIO 5: Cancelar cita
  # ==========================================
  Scenario: Cancelar una cita pendiente
    Given existe una cita pendiente con identificador "3"
    When el cliente cancela la cita con identificador "3"
    Then el sistema cambia el estado de la cita a "cancelada"
    And el sistema libera el horario para nuevas reservas

  # ==========================================
  # ESCENARIO 6: Listar citas
  # ==========================================
  Scenario: Listar todas las citas de un cliente
    Given el cliente "Ana Lopez" tiene 2 citas registradas
    When el cliente solicita ver sus citas
    Then el sistema muestra una lista con 2 citas
    And cada cita muestra fecha, hora, mascota y estado