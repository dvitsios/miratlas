#!/usr/bin/perl

print "Content-type:text/html\n\n";


print "<HTML>\n";
print "TEST\n";

my $hostname =`uname -a`;

print "<PRE>\n";
print "$hostname\n";



my $r_script= "test.R";
my $path="$r_script";

save_R_env();


print "</PRE>\n";
print "</HTML>\n";


sub save_R_env {
    my $execute = `Rscript $path`;
    print "$? if $?";
    print "$execute\n";
}
