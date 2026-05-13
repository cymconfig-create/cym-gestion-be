#!/usr/bin/env bash
set -euo pipefail

BASE="${BASE:-http://127.0.0.1:8000/api}"
LOGIN_INPUT="${LOGIN_INPUT:-admin@cym.local}"
LOGIN_PASSWORD="${LOGIN_PASSWORD:-admin123}"

echo "BASE=$BASE"
echo "LOGIN_INPUT=$LOGIN_INPUT"

TOKEN="$(curl -s -X POST "$BASE/auth/authenticate" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$LOGIN_INPUT\",\"password\":\"$LOGIN_PASSWORD\"}" | jq -r '.data // empty')"

if [[ -z "$TOKEN" ]]; then
  echo "ERROR: login failed. Response:"
  curl -s -X POST "$BASE/auth/authenticate" \
    -H "Content-Type: application/json" \
    -d "{\"email\":\"$LOGIN_INPUT\",\"password\":\"$LOGIN_PASSWORD\"}" | jq .
  exit 1
fi

echo "TOKEN ok (${#TOKEN} chars)"

check() {
  local path="$1"
  local label="$2"
  local out
  out="$(curl -s "$BASE/$path" -H "Authorization: Bearer $TOKEN")"
  local status
  status="$(echo "$out" | jq -r '.status // "null"')"
  local error
  error="$(echo "$out" | jq -r '.error // "null"')"
  echo "$(printf '%-18s' "$label") status=$status error=$error"
}

check "auth/me" "auth/me"
check "user" "user"
check "profile" "profile"
check "selector" "selector"
check "company" "company"
check "employee" "employee"
check "attachment" "attachment"
check "conversation" "conversation"
check "event" "event"

echo "Smoke test finished."

