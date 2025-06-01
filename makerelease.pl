#!/usr/bin/perl

use warnings;
use strict;

use Getopt::Long;

my $literal_tag;

GetOptions('tag=s' => \$literal_tag) or die "Invalid options\n";

my $rel_ver = shift or die "No version specified!\n";

my $tag;

if ($literal_tag)
{
  $tag = $literal_tag;
}
else
{
  $tag = "v$rel_ver";
}

system(
       'git',
       'archive',
       '--format=tar.gz',
       "--prefix=mrbs-$rel_ver/",
       '-o',
       "mrbs-$rel_ver.tar.gz",
       $tag
      );
system(
       'git',
       'archive',
       '--format=zip',
       "--prefix=mrbs-$rel_ver/",
       '-o',
       "mrbs-$rel_ver.zip",
       $tag
      );
