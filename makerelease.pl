#!/usr/bin/perl

use warnings;
use strict;

my $rel_ver = shift or die "No version specified!\n";

my $tag = "v$rel_ver";

system(
       'git',
       'archive',
       '--format=tar.gz',
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
