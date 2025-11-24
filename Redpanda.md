## Redpanda Docker

```bash
docker compose -f .docker/redpanda/docker-compose.yml up -d

# if you want turn off
docker stop redpanda && docker stop redpanda-console

# if you want turn on again
docker start redpanda && docker start redpanda-console
```