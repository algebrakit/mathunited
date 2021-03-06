#!/bin/zsh
#
# Part of Math4All authoring tool.
#
# This script commits the changes in the paragraph communicated through ARG1
#

# Avoid locale errors
LC_CTYPE=

# Define println function that writes to output. If gawk is present the lines
# are prependend with a time stamp
if ! type gawk > /dev/null; then
function println() {
    cat
}
else
function println() {
    gawk '{ print strftime("%Y-%m-%d|%H:%M:%S|"), $0; fflush(); }'
}
fi

# Capture all output
{
    echo "=== SVN COMMIT OF PARAGRAPH '${ARG1}'"
    if test ! -d "${ARG1}"; then
      echo "!!! '${ARG1}' is not a directory. Stopping."
      exit 1
    fi

    echo "--- Running 'svn add' on path ${ARG1}"
    # List of directories that need to be committed
    commitdirs=($ARG1)



    #
    # 1. XML FILES
    #

    # Add all XML files except index.xml
    echo "... Adding XML files"
    list=$(find ${ARG1} -maxdepth 1 -iname "*.xml" -and -not -name index.xml)
    svn add --parents --force ${(f)list}

    #
    # 2. MEDIA FILES
    #

    # Parent dir
    base=${ARG1:h}
    # Image directory
    imgdir=${base}/images/highres
    # Geogebra dir
    ggbdir=${base}/geogebra
    # Other documents
    doxdir=${base}/dox

    # Add images
    for extradir in $imgdir $ggbdir $doxdir;  do
      if test -d ${extradir}; then
        echo "... Adding media in ${extradir:t}"
        list=$(find ${extradir} -maxdepth 1 -type f -not -name ".*")
        if test ! -z "$list"; then
          svn add --parents --force ${(f)list}
          commitdirs+=(${extradir})
        fi
      fi
    done

    #
    # Fixup XML
    #
    echo "=== XML FIXUP OF PARAGRAPH '${ARG1}'"
    if test ! -d "${ARG1}"; then
      echo "!!! '${ARG1}' is not a directory. Stopping."
      exit 1
    fi

    echo "--- Fixing xsi:noNamespaceSchemaLocation on  all XML in ${ARG1}"
    # Process top level XML files except for index.xml
    for f in $(find ${ARG1} -maxdepth 1 -iname "*.xml" -and -not -name index.xml); do
      # Only process files with wrong xsi tag
      if grep -q xsi:nonamespaceschemalocation ${f}; then
        echo "--- ... ${f:t}"
        # Run inplace sed and delete backup only if successful
        sed -i.bak -e 's/xsi:nonamespaceschemalocation/xsi:noNamespaceSchemaLocation/g' $f && rm ${f}.bak
      fi
    done

    #
    # Update & commit
    #
    echo "--- Running 'svn update' on path ${ARG1}"
    svn update --accept mine-conflict ${ARG1} $imgdir $ggbdir $doxdir
    echo "--- Running 'svn commit' on path ${ARG1}"
    svn commit ${commitdirs} -m "Changes by user $ARG2."


    #
    # Unlocking files is delayed until paragraph lock is removed (see svn-unlock-file)
    #
    #xmllist=$(find ${ARG1} -maxdepth 1 -iname "*.xml" -and -not -name index.xml)
    #svn unlock ${(f)xmllist}


    echo "*** DONE. SVN COMMIT OF PARAGRAPH '${ARG1}'"
} |& println
