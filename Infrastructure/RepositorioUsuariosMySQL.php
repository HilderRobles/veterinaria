<?php
namespace Infrastructure;

use PDO;
use Domain\Usuario;
use Domain\RepositorioUsuarios;

class RepositorioUsuariosMySQL
implements RepositorioUsuarios {

    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function guardar(Usuario $usuario) {

        $sql = "
            INSERT INTO usuarios(
                nombre,
                email,
                telefono,
                password_hash,
                rol
            )
            VALUES(?,?,?,?,?)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $usuario->getNombre(),
            $usuario->getEmail(),
            $usuario->getTelefono(),
            $usuario->getPasswordHash(),
            $usuario->getRol()
        ]);

        return true;
    }

    public function buscarPorId($id) {

        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuarios
             WHERE id_usuario = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar() {

        $stmt = $this->pdo->query(
            "SELECT * FROM usuarios"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id) {

        $stmt = $this->pdo->prepare(
            "DELETE FROM usuarios
             WHERE id_usuario = ?"
        );

        return $stmt->execute([$id]);
    }
}