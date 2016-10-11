#!/usr/bin/perl

print "Content-type: text/html\n\n";

open( my $FH, '>tmp.txt');
print $FH "heyho, test passed!";
close(FH);

print <<"EOF";
<HTML>

<HEAD>
<TITLE>Hello, world!</TITLE>
</HEAD>

<BODY>
<H1>Hello, world!</H1>
</BODY>

</HTML>
EOF
