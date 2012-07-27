#!/usr/bin/perl

use warnings;
use strict;

# Config
my $repos_base = 'svn://svn.code.sf.net/p/mrbs/code/';
my $rel_ver = shift;
my $user = shift || 'jberanek';

my $tag = $rel_ver;

$tag =~ s/\./_/g;

$tag = 'mrbs-'.$tag;

(system('svn',
        'copy',
        "${repos_base}mrbs/trunk",
        "${repos_base}/mrbs/tags/$tag") == 0) or
    die "Failed to create tag in SVN\n";
