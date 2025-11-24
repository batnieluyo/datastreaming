# POC Data Streaming
Este proyecto usa redpanda como servicio de datastraming

## 🐇 RabbitMQ
RabbitMQ es un **message broker tradicional** basado en colas.  
Su enfoque principal es **enviar mensajes de un punto A a un punto B** de manera confiable.

### Características clave:
- Usa **colas** para almacenar mensajes.
- El broker es **inteligente**: decide cómo enrutar y distribuir mensajes.
- Los consumidores son **simples**: reciben lo que RabbitMQ les envía (push).
- Excelente para **tareas asincrónicas**, **microservicios**, trabajos que requieren:
    - reintentos,
    - dead-letter queues,
    - mensajes programados,
    - enrutamiento mediante exchanges (fanout, topic, direct, etc.).
- Garantiza orden **solo si un consumidor único** lee la cola.
- Es ideal cuando quieres **procesamiento confiable**, no tanto analítico.

### Cuándo usar RabbitMQ:
- Procesamiento de tareas internas (jobs).
- Microservicios que requieren garantía de entrega.
- Sistemas donde el orden no es crítico o se controla por diseño.
- Cuando necesitas reintentos y DLQ automáticos.

---

## 🔥 Apache Kafka
Kafka es una **plataforma de streaming distribuido** diseñada para manejar grandes volúmenes de datos en tiempo real.

### Características clave:
- Almacena mensajes en **topics** divididos en **particiones**.
- El broker es **simple** (dumb): solo guarda mensajes secuenciales.
- Los consumidores son **inteligentes**: controlan offsets, ritmo y relectura.
- Es ideal para **altos volúmenes**, **event sourcing**, **logs**, **telemetría** y **análisis en tiempo real**.
- Permite hacer **replay**: puedes volver a leer mensajes históricos.
- Escala horizontalmente de forma masiva agregando brokers y particiones.

### Cuándo usar Kafka:
- Sistemas de alta escala y datos en streaming.
- Arquitecturas event-driven masivas.
- Analítica en tiempo real (clicks, logs, sensores).
- Procesamiento distribuido (Flink, Spark).
- Cuando necesitas reproducir eventos pasados.


# Apendice

## 🔵 Broker
**Smart (RabbitMQ):**  
El broker toma decisiones: gestiona colas, orden, reintentos y distribución.

**Dumb (Kafka):**  
El broker solo almacena mensajes; deja la lógica al consumidor.

---

## 🟠 Consumer
**Dumb (RabbitMQ):**  
El consumidor solo recibe lo que le envía el broker.

**Smart (Kafka):**  
El consumidor decide cuánto leer, desde dónde y cómo procesar.

---

## 🧲 Tipo de consumer
**Push-based (RabbitMQ):**  
El broker empuja los mensajes a los consumidores.

**Pull-based (Kafka):**  
El consumidor pide (pull) los mensajes cuando está listo.

---

## 🔗 Orden garantizado
**RabbitMQ:**  
Garantiza orden únicamente si hay un solo consumidor por cola.

**Kafka:**  
Garantiza orden dentro de una partición (1 partición → orden perfecto).

---

## 🔄 Evita duplicados
**RabbitMQ:**  
No garantiza evitar duplicados.

**Kafka:**  
Evita duplicados por partición gracias a sus offsets.

---

## 🔁 Reintentos automatizados
**RabbitMQ:**  
Los reintentos son nativos (TTL, requeue, retrasos).

**Kafka:**  
Debes implementarlos tú mismo; no vienen de fábrica.

---

## ☠️ Gestión de Dead Letters (DLQ)

Una **Dead Letter Queue (DLQ)** es una cola especial donde se envían los mensajes que **no pudieron ser procesados correctamente** después de varios intentos.

En lugar de perderse o bloquear el sistema, los mensajes problemáticos se mueven a esta cola para:

- Revisarlos manualmente
- Analizarlos
- Reprocesarlos más tarde
- Detectar errores en la aplicación
- Evitar que un mensaje defectuoso bloquee toda la cola principal  

### ¿Por qué un mensaje llega a una DLQ?
Un mensaje puede terminar en una DLQ cuando:
- Ha fallado demasiadas veces al ser procesado
- Su formato es incorrecto
- Le faltan datos obligatorios
- El consumidor genera errores constantes
- Expira su tiempo de vida (TTL)

### DLQ en RabbitMQ
RabbitMQ permite configurar DLQ **de manera nativa**:  
cuando un mensaje falla repetidamente o vence su TTL, se envía automáticamente a una cola especial marcada como "dead-letter".

### DLQ en Kafka
Kafka **no tiene DLQ nativa**, pero se simula creando un *topic* aparte  
(por ejemplo: `topic-name.DLQ`)  
y enviando manualmente los mensajes que el consumidor no pudo procesar.

La lógica de "mensajes muertos" la implementa el desarrollador, no Kafka.

**RabbitMQ:**  
Incluye DLQ nativa cuando un mensaje falla repetidamente.

**Kafka:**  
No tiene DLQ nativa; se simula creando un topic especial.

---

## 🕒 Mensajes programados
**RabbitMQ:**  
Permite programar mensajes usando un plugin (delayed message exchange).

**Kafka:**  
No soporta mensajes programados nativamente.

---

## 💾 Persistencia de mensajes (replay)
**RabbitMQ:**  
Una vez consumido, el mensaje desaparece; no puedes reproducirlo.

**Kafka:**  
Puedes volver a leer mensajes desde cualquier offset (histórico).

---

## 📈 Escalabilidad
**RabbitMQ:**  
Escala muy bien verticalmente (más recursos), pero horizontalmente es limitado.

**Kafka:**  
Escala horizontalmente de forma masiva usando particiones y múltiples brokers.


# 🟦 Comparativa: RabbitMQ vs Kafka

* 🟩 = Bueno / Sí
* 🟥 = Malo / No
* 🟧 = Depende / Parcial

| Característica                         | RabbitMQ                                   | Kafka                                           |
|----------------------------------------|---------------------------------------------|------------------------------------------------|
| **Broker**                             | 🟩 Smart                                    | 🟥 Dumb                                         |
| **Consumer**                           | 🟥 Dumb                                     | 🟩 Smart                                        |
| **Tipo de consumer**                   | 🟦 Push-based                               | 🟦 Pull-based                                   |
| **Orden garantizado**                  | 🟧 Sí* (1 consumer)                         | 🟧 Sí* (1 partición/consumer)                   |
| **Evita duplicados**                   | 🟥 No                                       | 🟧 Sí* (por partición)                          |
| **Reintentos automatizados**           | 🟩 Sí                                       | 🟥 No                                           |
| **Gestión Dead Letters**               | 🟩 Sí                                       | 🟥 No                                           |
| **Mensajes programados**               | 🟧 Sí* (con plugin)                         | 🟥 No                                           |
| **Persistencia de mensajes (replay)**  | 🟥 No                                       | 🟩 Sí                                           |
| **Escalabilidad**                      | 🟧 Vertical OK. Horizontal limitada         | 🟩 Muy escalable                                |

