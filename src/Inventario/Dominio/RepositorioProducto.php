<?php

namespace App\Inventario\Dominio;

interface RepositorioProducto {
    public function guardar(Producto $producto): void;
    public function buscarPorId(string $id): ?Producto;
}