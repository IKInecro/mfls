#!/usr/bin/env bash
# Reset state uji QA di DB staging (bukan production).
# Pakai: ./tests/k6/reset.sh
set -euo pipefail
cd "$(dirname "$0")/../.."

ENV_FILE="${ENV_FILE:-.env}"
DB_STAGING="${DB_STAGING:-mfls_staging}"

export MYSQL_PWD=$(grep -m1 '^DB_PASSWORD=' "$ENV_FILE" | cut -d= -f2- | tr -d '"')
DB_USER=$(grep -m1 '^DB_USERNAME=' "$ENV_FILE" | cut -d= -f2- | tr -d '"')
DB_HOST=$(grep -m1 '^DB_HOST=' "$ENV_FILE" | cut -d= -f2- | tr -d '"')

if [[ "$DB_STAGING" != mfls_staging ]]; then
  echo "Refusing: DB_STAGING must be mfls_staging" >&2
  exit 1
fi

mysql -h"$DB_HOST" -u"$DB_USER" "$DB_STAGING" <<'SQL'
DELETE j FROM jawaban_ujian j JOIN peserta p ON p.id = j.peserta_id JOIN akun a ON a.id = p.akun_id WHERE a.email LIKE 'qa%@example.test';
DELETE n FROM nilai_ujian n JOIN peserta p ON p.id = n.peserta_id JOIN akun a ON a.id = p.akun_id WHERE a.email LIKE 'qa%@example.test';
DELETE s FROM sesi_ujian s JOIN peserta p ON p.id = s.peserta_id JOIN akun a ON a.id = p.akun_id WHERE a.email LIKE 'qa%@example.test';
DELETE FROM sesi_ujian WHERE peserta_id NOT IN (SELECT id FROM peserta);
DELETE FROM sessions;
SQL

echo "QA state reset in $DB_STAGING"
