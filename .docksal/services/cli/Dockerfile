# Allows adding or updating software in CLI image,
# example below updates platformsh-cli to latest version.
# To use, update FROM and RUN as needed, then copy following to docksal.yml under services:
  # cli:
  #   volumes:
  #     # Project root volume
  #     - project_root:/var/www:rw,nocopy,cached
  #     # Shared ssh-agent socket
  #     - docksal_ssh_agent:/.ssh-agent:ro
  #   environment:
  #     - XDEBUG_ENABLED=${XDEBUG_ENABLED:-0}
  #     - XDEBUG_CONFIG=client_host=${DOCKSAL_HOST_IP} client_port=9000
  #     - XDEBUG_MODE=debug
  #   # Run Dockerfile to add or udpdate software.
  #   build: services/cli

# Use a stock Docksal php8.1-3 image as the base
FROM docksal/cli:php8.1-3

RUN set -e; \
    platform self:update -y || echo "Please update the platform.sh CLI on your host sytem as well by doing a platform self:update"
