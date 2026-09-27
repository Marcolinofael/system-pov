#!/bin/bash
# Backup diário do banco e das fotos para o Google Drive (rclone).
#
# Variáveis (vindas do docker-compose.prod.yml / Environment do Dokploy):
#   DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#   GDRIVE_PASTA   pasta no Drive (padrão: backups-system-pov)
#   BACKUP_HORA    horário diário HH:MM (padrão: 03:00)
#   BACKUP_DIAS    quantos dias manter no Drive (padrão: 30)
#   BACKUP_AO_INICIAR  1 = faz um backup assim que o container sobe (padrão: 1)
# O acesso ao Drive vem de RCLONE_CONFIG_GDRIVE_* (token em GDRIVE_TOKEN).

set -uo pipefail

PASTA="${GDRIVE_PASTA:-backups-system-pov}"
HORA="${BACKUP_HORA:-03:00}"
DIAS="${BACKUP_DIAS:-30}"
DESTINO="gdrive:${PASTA}"

log() { echo "[$(date '+%d/%m/%Y %H:%M:%S')] $*"; }

fazer_backup() {
    local ts tmp
    ts="$(date +%Y-%m-%d_%H%M)"
    tmp="/tmp/backup-${ts}"
    mkdir -p "$tmp"

    log "Iniciando backup ${ts}"

    if ! MYSQL_PWD="$DB_PASSWORD" mysqldump -h "$DB_HOST" -u "$DB_USERNAME" \
            --single-transaction --routines --triggers --no-tablespaces \
            --databases "$DB_DATABASE" | gzip > "${tmp}/banco-${ts}.sql.gz"; then
        log "ERRO: falha ao exportar o banco. Backup cancelado."
        rm -rf "$tmp"
        return 1
    fi

    if ! tar -czf "${tmp}/arquivos-${ts}.tar.gz" -C /dados storage; then
        log "ERRO: falha ao compactar as fotos. Backup cancelado."
        rm -rf "$tmp"
        return 1
    fi

    log "Arquivos gerados:"
    du -h "$tmp"/*

    if ! rclone copy "$tmp" "${DESTINO}/${ts}" --stats-one-line -q; then
        log "ERRO: falha ao enviar para o Google Drive (verifique GDRIVE_TOKEN)."
        rm -rf "$tmp"
        return 1
    fi
    rm -rf "$tmp"
    log "Enviado para o Google Drive: ${PASTA}/${ts}"

    # Retenção: apaga backups mais antigos que BACKUP_DIAS
    rclone delete "$DESTINO" --min-age "${DIAS}d" -q || log "Aviso: não foi possível limpar backups antigos."
    rclone rmdirs "$DESTINO" --leave-root -q || true

    log "Backup ${ts} concluído."
}

segundos_ate_proximo() {
    local agora alvo
    agora=$(date +%s)
    alvo=$(date -d "today ${HORA}" +%s)
    [ "$alvo" -le "$agora" ] && alvo=$(date -d "tomorrow ${HORA}" +%s)
    echo $(( alvo - agora ))
}

if [ -z "${RCLONE_CONFIG_GDRIVE_TOKEN:-}" ]; then
    log "GDRIVE_TOKEN não configurado: backup desativado até a variável ser preenchida no Dokploy."
    exec sleep infinity
fi

# Versões novas do "rclone authorize" entregam o resultado em base64
# ({"client_id":"","client_secret":"","token":"{...}"}). Aceita esse formato também.
case "$RCLONE_CONFIG_GDRIVE_TOKEN" in
    \{*) ;;
    *)
        b64=$(printf '%s' "$RCLONE_CONFIG_GDRIVE_TOKEN" | tr -d ' \r\n' | tr -- '-_' '+/')
        while [ $(( ${#b64} % 4 )) -ne 0 ]; do b64="${b64}="; done
        token=$(printf '%s' "$b64" | base64 -d 2>/dev/null \
            | sed -n 's/.*"token":"\(.*\)"}[[:space:]]*$/\1/p' | sed 's/\\"/"/g')
        if [ -z "$token" ]; then
            log "ERRO: GDRIVE_TOKEN em formato desconhecido. Cole o resultado do 'rclone authorize'."
            exec sleep infinity
        fi
        export RCLONE_CONFIG_GDRIVE_TOKEN="$token"
        log "Token do Google Drive decodificado do formato base64."
        ;;
esac

log "Agendador de backup ativo: todo dia às ${HORA}, mantendo ${DIAS} dias em '${PASTA}'."

if [ "${BACKUP_AO_INICIAR:-1}" = "1" ]; then
    fazer_backup
fi

while true; do
    espera=$(segundos_ate_proximo)
    log "Próximo backup em $(( espera / 3600 ))h$(( (espera % 3600) / 60 ))min."
    sleep "$espera"
    fazer_backup
done
