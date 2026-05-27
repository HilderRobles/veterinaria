<?php
namespace Domain;

interface RepositorioProductos {

    public function guardar(Producto $producto);

    public function actualizar(Producto $producto);

    public function buscarPorId($id);

    public function listar();
}