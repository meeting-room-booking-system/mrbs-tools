#!/usr/bin/perl

use warnings;
use strict;

use File::Path 2.06;
use File::Find;
use Archive::Tar;
use Archive::Zip qw(:CONSTANTS);

my $rel_ver = shift or die "No version specified!\n";
my $user = shift || 'jberanek';

my $tag = $rel_ver;

$tag =~ s/\./_/g;

$tag = 'mrbs-'.$tag;

#if (-d 'mrbs-'.$rel_ver)
#{
#  File::Path::remove_tree('mrbs-'.$rel_ver);
#}

#(system('svn',
#        'export','http://svn.code.sf.net/p/mrbs/code/mrbs/tags/'.$tag,'mrbs-'.$rel_ver) == 0) or die "Failed to export from SVN\n";

my $tar_filename = 'mrbs-'.$rel_ver.'.tar.gz';
my $zip_filename = 'mrbs-'.$rel_ver.'.zip';

# Delete the old tar file, if it exists
if (-f $tar_filename)
{
  unlink $tar_filename;
}

# Delete the old ZIP file, if it exists
if (-f $zip_filename)
{
  unlink $zip_filename;
}

my $zip = Archive::Zip->new();
my $tar = Archive::Tar->new();

File::Find::find({no_chdir => 1, wanted => \&wanted}, 'mrbs-'.$rel_ver);

$tar->write($tar_filename, COMPRESS_GZIP);
$zip->writeToFileNamed($zip_filename);



##
# File::Find wanted function to build the ZIP and TAR release files
sub wanted
{
  my $file = $_;

  # Adding to the tar is simple...
  $tar->add_files($File::Find::name);

  # ... whereas the ZIP needs special treatment
  if (-f $file)
  {
    # Binary files are added verbatim
    if ($file =~ m/\.(gif|jpg|png|ics)$/)
    {
      $zip->addFile($File::Find::name, $File::Find::name,
                    COMPRESSION_LEVEL_BEST_COMPRESSION);
    }
    else
    {
      # All other files we read in and convert to CRLF line endings

      local $/ = undef;
      open FILE,'<',$File::Find::name or
        die "Failed to open '$File::Find::name' for reading!\n";
      my $file_data = <FILE>;
      close FILE;
      $file_data =~ s/\n/\r\n/g;
      $zip->addString($file_data, $File::Find::name,
                      COMPRESSION_LEVEL_BEST_COMPRESSION);
    }
  }
}
