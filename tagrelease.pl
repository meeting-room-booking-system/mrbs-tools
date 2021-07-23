#!/usr/bin/perl

use warnings;
use strict;

# Config
my $rel_ver = shift;

my $tag = $rel_ver;

$tag =~ s/\./_/g;

$tag = 'mrbs-'.$tag;

(system('git',
        'tag',
        '-a',
        '-m',
        "MRBS $rel_ver",
        $tag) == 0) or
    die "Failed to create tag\n";

print "Tag created, need to push outgoing change:\n\n";
(system('git',
        'log',
        '--not',
        '--remotes') == 0) or
    die "Failed to determine outgoing changes\n";
