<?php
namespace Domain;

interface RepositorioVentas {

    public function guardar(Venta $venta);

    public function buscarPorId($id);

    public function listar();
}