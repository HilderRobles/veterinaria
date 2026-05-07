# Feature: Registro de Mascotas
# Como cliente de la clínica veterinaria
# Quiero registrar a mi mascota en el sistema
# Para que pueda agendar citas y recibir atención médica

Feature: Registro de Mascotas

  Background:
    Given el sistema está funcionando correctamente
    And existe un cliente autenticado con nombre "Ana Lopez"

  # ==========================================
  # ESCENARIO 1: Camino feliz - Registrar mascota
  # ==========================================
  Scenario: Registrar una nueva mascota exitosamente
    Given la mascota no está registrada previamente
    When el cliente proporciona los datos:
      | nombre | especie | raza | edad |
      | Luna | Perro | Labrador | 3 |
    Then el sistema registra la mascota "Luna" en el sistema
    And el sistema asigna un identificador único a la mascota
    And el sistema vincula la mascota al cliente "Ana Lopez"

  # ==========================================
  # ESCENARIO 2: Ruta de error - Mascota sin nombre
  # ==========================================
  Scenario: Intentar registrar una mascota sin nombre
    When el cliente proporciona los datos:
      | nombre | especie | raza | edad |
      | | Perro | Labrador | 3 |
    Then el sistema rechaza el registro
    And el sistema muestra el mensaje "El nombre de la mascota es requerido"

  # ==========================================
  # ESCENARIO 3: Ruta de error - Especie inválida
  # ==========================================
  Scenario: Intentar registrar una mascota con especie vacía
    When el cliente proporciona los datos:
      | nombre | especie | raza | edad |
      | Luna | | Labrador | 3 |
    Then el sistema rechaza el registro
    And el sistema muestra el mensaje "La especie de la mascota es requerida"