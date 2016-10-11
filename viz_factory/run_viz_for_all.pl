#!/usr/bin/env perl

$input_dir = $ARGV[0];

opendir(DIR, $input_dir) or die $!;

while (my $file = readdir(DIR)) {

    next unless ($file =~ m/\.txt$/);
    $file = $input_dir."/$file";

    print "Rscript get_stacked_mods_profile.R $file viz_input\n";

    `Rscript get_stacked_mods_profile.R $file viz_input`;
}

closedir(DIR);
