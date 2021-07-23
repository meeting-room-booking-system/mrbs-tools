#!/usr/bin/perl

use warnings;
use strict;

my $rel_ver = shift or die "No version specified!\n";

my $tag = $rel_ver;

$tag =~ s/\./_/g;

$tag = 'mrbs-'.$tag;

system(
       'git',
       'archive',
       '--format=tar',
       '-o',
       "mrbs-$rel_ver.tar.gz",
       $tag
      );
system(
       'git',
       'archive',
       '--format=zip',
       '-o',
       "mrbs-$rel_ver.zip",
       $tag
      );
