#!/bin/bash
sleep 5
rpk topic create test-topic --brokers=redpanda:9092
rpk topic create events --brokers=redpanda:9092