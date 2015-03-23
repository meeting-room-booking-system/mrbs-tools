#!/usr/bin/perl

use warnings;
use strict;

my $rel_ver = shift or die "No version specified!\n";

my $tag = $rel_ver;

$tag =~ s/\./_/g;

$tag = 'mrbs-'.$tag;

system('hg',
      'archive',
      '-r', $tag,
      "mrbs-$rel_ver.tar.gz");
system('hg',
      'archive',
      '-r', $tag,
      "mrbs-$rel_ver.zip");
