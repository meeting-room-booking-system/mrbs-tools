#!/usr/bin/perl

use MIME::Lite;
use XML::Simple;
use LWP::UserAgent;
use FindBin;

# Config
my $project = 'mrbs';
my $svn_repos = 'code';
my @terse_recipients =
(
 'mrbs-general@lists.sourceforge.net',
#  'jberanek',
);
my @verbose_recipients =
(
  'mrbs-commits@lists.sourceforge.net',
#  'jberanek',
);

# Calculated values
my $repos_url = "svn://svn.code.sf.net/p/$project/$svn_repos/";
my $commit_url_base = "https://sourceforge.net/p/$project/$svn_repos/";
my $rss_url = "${commit_url_base}feed";

my $rev_range = shift;
if ($rev_range)
{
  if ($rev_range =~ m/(\d+):(\d+)/)
  {
    $start_rev = $1;
    $end_rev = $2;
  }
  elsif ($rev_range =~ m/(\d+)/)
  {
    $start_rev = $end_rev = $1;
  }
  for (my $rev = $start_rev; $rev <= $end_rev; $rev++)
  {
    email_commit($rev);
  }
  exit 0;
} 

my $ua = LWP::UserAgent->new;
my $response = $ua->get($rss_url);
if (!$response->is_success)
{
  die "RSS fetch failed: ".$response->status_line."\n";
}
my $rss = $response->decoded_content();

my $xml_ref = XMLin($rss);

my @revisions;

foreach my $item (@{$xml_ref->{channel}->{item}})
{
  if ($item->{description} =~ m|<a href="/p/.*?/(\d+)/|)
  {
    push @revisions, $1;
  }
}

my @sorted_revisions = sort @revisions;

my $last_rev_filename = "$FindBin::Bin/$project-$svn_repos.lastrev";
my $last_rev = 0;
my $ok = open LAST_REV,'<',$last_rev_filename;
if ($ok)
{
  $last_rev = <LAST_REV>;
  chomp $last_rev;
  close LAST_REV;
}

foreach my $revision (@sorted_revisions)
{
  if ($revision > $last_rev)
  {
    email_commit($revision);
    $last_rev = $revision;
  }
}

open LAST_REV,'>',$last_rev_filename;
print LAST_REV "$last_rev\n";
close LAST_REV;

# The end


#
sub email_commit
{
  my ($rev) = @_;
  
  my $terse_info = "Commit r$rev: $commit_url_base$rev/\n\n";
  my $author = 'unknown';
  
  open SVN,'-|',
    'svn',
    'log',
    '-c', $rev,
    '-v',
    $repos_url;
  while (<SVN>)
  {
    if (m/^r\d+\s+\|\s+(\S+)/)
    {
      $author = $1;
    }
    $terse_info .= $_;
  }
  close SVN;
  
  my $msg = MIME::Lite->new(From => "$author\@users.sourceforge.net",
                            To => \@terse_recipients,
                            Subject => "SF.net SVN: $project:[$rev]",
                            Data => $terse_info);
  $msg->send();

  my $verbose_info = $terse_info;
  
  $verbose_info .= "\n";
  
  open SVN,'-|',
    'svn',
    'diff',
    '-c', $rev,
    $repos_url;
  while (<SVN>)
  {
    $verbose_info .= $_;
  }
  close SVN;

  $msg = MIME::Lite->new(From => "$author\@users.sourceforge.net",
                         To => \@verbose_recipients,
                         Subject => "SF.net SVN: $project:[$rev]",
                         Data => $verbose_info);
  $msg->send();
    
}
