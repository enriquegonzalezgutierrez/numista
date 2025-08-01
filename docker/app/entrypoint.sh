#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Execute the main container command (supervisord).
# The `exec` command replaces the shell process with the supervisord process,
# which is the correct way to run the main process in a container.
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf