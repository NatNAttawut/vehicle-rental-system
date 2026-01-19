#!/usr/bin/env bash
set -euo pipefail
EXCLUDE_REGEX='(header\.php|Admin/Aheader\.php|User/Uheader\.php|Signup/signup\.php|Signin/signin\.php)$'
find . -type f -name '*.php' | grep -Ev "$EXCLUDE_REGEX" | while IFS= read -r f; do
  perl -pi -e 'if (/<script/i && /supabase-js/i) {$_="";}' "$f"
  perl -pi -e 'if (/<script/i && m{assets/js/(supabaseClient|ui-auth|guard-user|guard-admin)\.js}i) {$_="";}' "$f"
  perl -pi -e 'if (/<script/i && m{/assets/js/}i) {$_="";}' "$f"
done
