#!/bin/bash

#
# This script will:
# 1. check if WP-CLI is available locally and on server
# 2. empty your local database (NO BACKUP! If you need a backup, extend this script.)
# 3. connect to server, dump the database using WP-CLI, pipe compressed data to localhost, uncompress and import using WP-CLI
# 4. deactivate specified plugins
# 5. delete spam, revisions, cache and transients
# 6. perform a search-replace for site URL and flush rewrite rules
# 7. create an admin user for development (dev / dev)
#

# Set server SSH connection parameters
ssh_connection="skye@cremoznik.si -p 52022"

# Set path to the WordPress installation directory on the server
remote_wordpress="/srv/http/cremoznik.si/current/web/wp"

function findWPCLI {
  command -v wp > /dev/null 2>&1 || { echo >&2 "==> WP-CLI needs to be available as 'wp' command in your PATH $1"; exit 1; }
}
findWPCLI locally
ssh $ssh_connection "$(typeset -f); findWPCLI 'on server'" || exit 1;

echo -e "==> Dropping local database…"
wp db reset --yes

echo -e "==> Importing database from production…"
ssh -C $ssh_connection "wp --path=$remote_wordpress db export -" | wp db import -

# Deactivate plugins you don't want locally
echo -e "==> Deactivating production only plugins…"
wp plugin deactivate redis-cache wordpress-seo

echo -e "==> Cleaning up…"
wp comment delete $(wp comment list --status=spam --format=ids) --force
wp db query "DELETE FROM wpcrean_posts WHERE post_type = 'revision'"
wp transient delete --all
wp cache flush

# Do a search-replace on the entire database for site URL
echo -e "==> Search/replace hostname…"
wp search-replace cremoznik.si crean.dev

echo -e "==> Flushing rewrite rules…"
wp rewrite flush

echo -e "==> Creating admin account for development (login: dev / dev)"
wp user create dev dev@dev.dev --user_pass=dev --role=administrator

echo -e "==> Done."
