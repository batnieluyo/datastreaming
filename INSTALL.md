# Instalación

## Redpanda

```bash
docker compose -f .docker/redpanda/docker-compose.yml up -d

# if you want turn off
docker stop redpanda && docker stop redpanda-console

# if you want turn on again
docker start redpanda && docker start redpanda-console
```

## RabbitMQ

```bash
docker compose -f .docker/rabbitmq/docker-compose.yml up -d

# if you want turn off
docker stop rabbitmq

# if you want turn on again
docker start rabbitmq
```

Broker (AMQP): amqp://admin:admin@localhost:5672/

UI: http://localhost:15672 (user/pass: admin / admin)

## Apache kafka

```bash
docker compose -f .docker/kafka/docker-compose.yml up -d

# if you want turn off
docker stop kafka-kraft && docker stop kafka-ui

# if you want turn on again
docker start kafka-kraft && docker start kafka-ui
```
http://localhost:8082