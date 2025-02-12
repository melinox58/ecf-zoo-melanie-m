FROM ghcr.io/mailpit/mailpit:v0.5.0

# Expose le port
EXPOSE 8025 1025

# Exécutez Mailpit
CMD ["Mailpit"]
