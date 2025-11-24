# Data Streaming

Este proyecto utiliza **Redpanda** como servicio de _data streaming_ para el envío, consumo y procesamiento de eventos en tiempo real.  
Redpanda es una alternativa moderna, ligera y totalmente compatible con Apache Kafka.

---

## 🚀 Redpanda

Este entorno incluye:

- **Redpanda Broker** — Servicio principal de streaming
- **Redpanda Console** — UI web para monitorear topics, mensajes y consumidores

## ⚙️ Laravel Streaming Service

Este proyecto utiliza **Laravel 12** con **PHP 8.4** para consumir y producir datos en un entorno de *data streaming* basado en **Redpanda**.

Para la comunicación con Kafka/Redpanda se utiliza la librería nativa:

👉 [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka)

Esta extensión provee soporte completo vía `librdkafka` y es requerida para ejecutar el servicio.

---

## ▶️ Ejecución del entorno Laravel


```bash
docker compose -f .docker/laravel/docker-compose.yml up --build
```

# Crear topic

- Visitar http://0.0.0.0:8080/topics
- Ingresar
  - Topic name: test-topic

# Escuchar el streaming

```bash
docker exec -it laravel-streaming sh
php artisan kafka:consume-service-a
```

> Redpanda console http://0.0.0.0:8080/

> laravel http://localhost:9090/

# Generar un mensaje

> http://localhost:9090/redpanda