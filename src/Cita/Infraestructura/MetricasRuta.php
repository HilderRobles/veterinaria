<?php
declare(strict_types=1);
namespace App\Cita\Infraestructura;

class MetricasRuta {
    public static function exponer(): void {
        header('Content-Type: text/plain; version=0.0.4');
        
        // Simulamos tráfico aleatorio para que las gráficas se muevan
        $totalRequests = rand(1000, 1500);
        $errorRequests = rand(1, 10); // Unos pocos errores 500
        $latencyBucket1 = rand(800, 900); // Rápidos (menos de 0.1s)
        $latencyBucket2 = rand(100, 200); // Normales (menos de 0.5s)
        $latencyBucket3 = rand(10, 50);   // Lentos (más de 0.5s)

        echo "# HELP http_requests_total Total de peticiones HTTP\n";
        echo "# TYPE http_requests_total counter\n";
        echo "http_requests_total{status=\"200\"} " . ($totalRequests - $errorRequests) . "\n";
        echo "http_requests_total{status=\"500\"} " . $errorRequests . "\n\n";

        echo "# HELP http_request_duration_seconds_bucket Histograma de latencia\n";
        echo "# TYPE http_request_duration_seconds_bucket histogram\n";
        echo "http_request_duration_seconds_bucket{le=\"0.1\"} " . $latencyBucket1 . "\n";
        echo "http_request_duration_seconds_bucket{le=\"0.5\"} " . ($latencyBucket1 + $latencyBucket2) . "\n";
        echo "http_request_duration_seconds_bucket{le=\"+Inf\"} " . ($latencyBucket1 + $latencyBucket2 + $latencyBucket3) . "\n";
    }
}