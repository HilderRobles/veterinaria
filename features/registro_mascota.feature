# Feature: Registro de Mascotas
# Como cliente de la clinica veterinaria
# Quiero registrar a mi mascota en el sistema
# Para poder agendar citas y recibir atencion medica

Feature: Registro de Mascotas

  Background:
    Given el sistema está funcionando correctamente
    And existe un cliente autenticado con nombre "Ana Lopez"

  Scenario: Registrar una nueva mascota exitosamente
    Given la mascota no esta registrada previamente
    When el cliente proporciona los datos:
      | nombre | especie | raza     | edad |
      | Luna   | Perro   | Labrador | 3    |
    Then el sistema registra la mascota "Luna" en el sistema
    And el sistema asigna un identificador unico a la mascota
    And el sistema vincula la mascota al cliente "Ana Lopez"

  Scenario: Intentar registrar una mascota sin nombre
    When el cliente proporciona los datos:
      | nombre | especie | raza     | edad |
      |        | Perro   | Labrador | 3    |
    Then el sistema rechaza el registro
    And el sistema muestra el mensaje "El nombre de la mascota es requerido"

  Scenario: Intentar registrar una mascota con especie vacia
    When el cliente proporciona los datos:
      | nombre | especie | raza     | edad |
      | Luna   |         | Labrador | 3    |
    Then el sistema rechaza el registro
    And el sistema muestra el mensaje "La especie de la mascota es requerida"