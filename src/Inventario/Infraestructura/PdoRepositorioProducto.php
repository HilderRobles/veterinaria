<?php

namespace App\Inventario\Infraestructura;

use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\RepositorioProducto;
use App\Inventario\Dominio\ObjetoValor\Precio;
use PDO;

class PdoRepositorioProducto implements RepositorioProducto {
    // Recibimos la conexión activa de la base de datos (PDO)
    public function __construct(private PDO $connection) {}

    // 1. Aquí se ejecuta el SQL para GUARDAR o ACTUALIZAR el producto
    public function guardar(Producto $producto): void {
        // Usamos una sentencia que inserta si es nuevo, o actualiza si el ID ya existe
        $sql = "INSERT INTO productos (id, nombre, descripcion, precio) 
                VALUES (:id, :nombre, :descripcion, :precio)
                ON DUPLICATE KEY UPDATE 
                    descripcion = :descripcion, 
                    precio = :precio";

        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            'id'          => $producto->id(),
            'nombre'      => $producto->nombre(),
            'descripcion' => $producto->descripcion(),
            'precio'      => $producto->precio()
        ]);
    }

    // 2. Aquí se ejecuta el SQL para BUSCAR un producto por su ID
    public function buscarPorId(string $id): ?Producto {
        $sql = "SELECT id, nombre, descripcion, precio FROM productos WHERE id = :id";
        
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);
        
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        // Si la base de datos no encontró nada, devolvemos null
        if (!$row) {
            return null;
        }

        // Si lo encontró, transformamos los datos planos de MySQL 
        // de vuelta a objetos limpios de nuestro Dominio
        return new Producto(
            $row['id'],
            $row['nombre'],
            $row['descripcion'],
            new Precio((float)$row['precio'])
        );
    }
}