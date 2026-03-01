

```shell
source .env.development
# Variables
NETWORK_NAME="${DOCKER_NETWORK}"
SUBNET="172.15.0.0/16"
GATEWAY="172.15.0.1"

docker network create \
    --driver bridge \
    --subnet="$SUBNET" \
    --gateway="$GATEWAY" \
    --opt "com.docker.network.bridge.name=${NETWORK_NAME}" \
    --label "description=Red compartida para todos los stacks Docker" \
    "$NETWORK_NAME"
    
docker network inspect "$NETWORK_NAME" --format='{{json .}}' 


```