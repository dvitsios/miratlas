#!/usr/bin/perl
$|=1;

use strict;

my $uuid = $ARGV[0];

my $input_csv = "../tmp/$uuid/csv_processed.counts";
my $output_processed_file = "../tmp/$uuid/processed.counts";

open(OUT, '>', $output_processed_file);
open(MY_CSV, '<', $input_csv);

my $line_cnt = 0;
my $total_depth = 0;

while(my $line = <MY_CSV>){

	chomp $line;

	if($line_cnt == 0){
		print OUT "Hits\t$uuid: processed.counts\n";

		$line_cnt++;
	} else{
		$line =~ s/\"//g;
		my @vals = split(',', $line);	
		print OUT "$vals[1]\t$vals[2]\n";
		$total_depth += $vals[2];
	}
	
}

print OUT "totaldepth\t$total_depth";

close(MY_CSV);
close(OUT);
