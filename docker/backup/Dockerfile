# Backup diário: mysqldump (cliente oficial do MySQL 8.4) + fotos -> Google Drive via rclone
FROM rclone/rclone:1 AS rclone

FROM mysql:8.4

RUN microdnf install -y tar gzip findutils && microdnf clean all

COPY --from=rclone /usr/local/bin/rclone /usr/local/bin/rclone
COPY backup.sh /usr/local/bin/backup.sh
RUN sed -i 's/\r$//' /usr/local/bin/backup.sh && chmod +x /usr/local/bin/backup.sh

# Substitui o entrypoint do MySQL: este container não roda banco, só o agendador de backup
ENTRYPOINT ["/usr/local/bin/backup.sh"]
