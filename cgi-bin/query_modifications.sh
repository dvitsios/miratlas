#!/bin/tcsh

echo "<html>"
echo "<body>"
setenv TMPDIR /net/isilonP/public/rw/homes/w3_enr01/enright-dev/miratlas/tmp

#Rscript /net/isilonP/public/rw/homes/w3_enr01/enright-dev/miratlas/cgi-bin/query_modifications.R

find /net/isilonP/public/rw/homes/w3_enr01/enright-dev/miratlas/tmp -mtime +1 -exec rm {} \;

Rscript /net/isilonP/public/rw/homes/w3_enr01/enright-dev/miratlas/cgi-bin/get_stacked_mods_profile.R $1

#echo "Rscript /net/isilonP/public/rw/homes/w3_enr01/enright-dev/miratlas/cgi-bin/get_stacked_mods_profile.R $1"


echo "</body>"
echo "</html>"
