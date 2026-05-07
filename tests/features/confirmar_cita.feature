# Feature: Confirmación de Citas
# Como administrador de la clínica veterinaria
# Quiero confirmar las citas solicitadas por los clientes
# Para organizar la agenda de atención

Feature: Confirmacion de Citas

  Background:
    Given el sistema está funcionando correctamente
    And existe un administrador autenticado

  # ==========================================
  # ESCENARIO 1: Camino feliz - Confirmar cita
  # ==========================================
  Scenario: Confirmar una cita pendiente exitosamente
    Given existe una cita pendiente con identificador "1"
    And la cita tiene fecha "2025-02-20" y hora "10:00:00"
    When el administrador confirma la cita
    Then el estado de la cita cambia a "confirmada"
    And el sistema envía una notificación al cliente

  # ==========================================
  # ESCENARIO 2: Ruta de error - Cita inexistente
  # ==========================================
  Scenario: Intentar confirmar una cita que no existe
    Given no existe una cita con identificador "999"
    When el administrador intenta confirmar la cita "999"
    Then el sistema rechaza la operación
    And el sistema muestra el mensaje "Cita no encontrada"

  # ==========================================
  # ESCENARIO 3: Cancelar cita confirmada (invariante)
  # ==========================================
  Scenario: Intentar cancelar una cita que ya está confirmada
    Given existe una cita confirmada con identificador "4"
    When el cliente intenta cancelar la cita "4"
    Then el sistema rechaza la operación
    And el sistema muestra el mensaje "No se puede cancelar cita confirmada"