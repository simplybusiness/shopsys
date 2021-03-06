#!/usr/bin/env bash

# Rewrite git history so that only commits that made changes in a subdirectory are kept and rewrite all filepaths as if it was root
# You can use arguments for "git rev-list" to specify what commits to rewrite (defaults to rewriting history of the checked-out branch)
# All tags in the provided range will be rewritten as well
#
# Usage: rewrite_history_from.sh <subdirectory> [<rev-list-args>]
#
# Example: rewrite_history_from.sh packages/alpha
# Example: rewrite_history_from.sh main-repository --branches

SUBDIRECTORY=$1
REV_LIST_PARAMS=`echo ${@:2} | tr " " "\n"`
echo "Rewriting history from a subdirectory '$SUBDIRECTORY'"

# Get directory of the other scripts
MONOREPO_SCRIPT_DIR=$(dirname "$0")
# Backup all monorepo tag refs because `git filter-branch` doesn't backup tags
$MONOREPO_SCRIPT_DIR/tag_refs_backup.sh

# Remove tags that don't contain any files in $SUBDIRECTORY
# Removes refs only, because tags are objects that can be used later after `original_refs_restore.sh`
# Removes also tags from $REV_LIST_PARAMS, so filter-branch filters only known tags (if they are present)
for TAG in `git tag`
do
    if [ `git ls-tree -r --name-only $TAG | grep -E "^\"*$SUBDIRECTORY/" | wc -l` == "0" ]; then
        git update-ref -d refs/tags/$TAG
        REV_LIST_PARAMS=`echo "$REV_LIST_PARAMS" | grep -v $TAG`
    fi
done

# All paths in the index that are not prefixed with a subdirectory are removed via "git rm --cached"
# Setting quotepath to false is needed to handle path and file names containing unicode characters
# If there are any files in the index all paths have the subdirectory prefix removed and the index is updated
# Previous index file is replaced by a new one (otherwise each file would be in the index twice)
# Only non-empty are filtered by the commit-filter
# The tags are rewritten as well as commits (the "cat" command will use original name without any change)
if [ $(uname) == "Darwin" ]; then
    XARGS_OPTS=""
    SED_OPTS="-E"
else
    XARGS_OPTS="-r"
    SED_OPTS="-r"
fi
SUBDIRECTORY=$SUBDIRECTORY SUBDIRECTORY_SED=${SUBDIRECTORY//-/\\-} TAB=$'\t' XARGS_OPTS=$XARGS_OPTS SED_OPTS=$SED_OPTS git filter-branch \
    --index-filter '
    git -c core.quotepath=false ls-files | grep -vE "^\"*$SUBDIRECTORY/" | xargs $XARGS_OPTS git rm -q --cached
    if [ "$(git ls-files)" != "" ]; then
        git ls-files -s | sed $SED_OPTS "s-($TAB\"*)$SUBDIRECTORY_SED/-\1-" | GIT_INDEX_FILE=$GIT_INDEX_FILE.new git update-index --index-info && mv $GIT_INDEX_FILE.new $GIT_INDEX_FILE
    fi' \
    --commit-filter 'git_commit_non_empty_tree "$@"' \
    --tag-name-filter 'cat' \
    -- $REV_LIST_PARAMS

# Moves backup monorepo tag refs to the same group with automatic backup from `git filter-branch`
# so tags can be restored by `original_refs_restore.sh`
$MONOREPO_SCRIPT_DIR/tag_refs_move_to_original.sh
