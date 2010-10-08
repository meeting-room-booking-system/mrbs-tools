#!/usr/bin/perl

use warnings;
use strict;

my $rel_ver = shift;
my $user = shift || 'jberanek';

my $tag = $rel_ver;

$tag =~ s/\./_/g;

$tag = 'mrbs-'.$tag;

(system('svn',
        'copy',
        'https://mrbs.svn.sourceforge.net/svnroot/mrbs/mrbs/trunk',
        'https://mrbs.svn.sourceforge.net/svnroot/mrbs/mrbs/tags/'.$tag) == 0) or
    die "Failed to create tag in SVN\n";
