#!/usr/bin/env perl

use strict;
use warnings;
use Cwd;
use File::Spec;
use File::Copy;
use Time::HiRes;
use Time::Format qw(%time %strftime %manip);
open(my $configfh, "<","libs/config.conf");
my @config_array = <$configfh>;
my %params = ();
foreach my $line (@config_array) {
	$line =~ s/\n//;
	my @split_line = split("=",$line);
	$params{$split_line[0]} = $split_line[1];
}
my $directory 			= $params{'jobs_directory'};
my $delim_path			= $params{'delim_path'};
my $raxml_abs_path		= $params{'raxml_abs_path'};
my $nw_utils_abs_path	= $params{'nw_utils_abs_path'};
my $process_name 		= $params{'process_name'};
my $max_jobs			= $params{'max_jobs'};
print $directory."\n";
while(1) {
	# Sleep
	Time::HiRes::sleep(2);
	
	# Check for running processes
	my @lines = qx/ps aux | grep $process_name/;
	my $proc_count 			= 0;
	my @current_processes 	= ();
	foreach my $line (@lines) {		
		if ($line =~ m/grep/) {
		} else {
			push(@current_processes, $line);
			$proc_count++;
		}
	}
	#~ exit;
	print "\n".$time{'hh:mm:ss - yyyy/mm/dd'}.": Jobs active: ".$proc_count;
	my $jobs_to_execute = $max_jobs - $proc_count;
	
	if($proc_count < $max_jobs) {
		opendir (DIR, $directory) or die $!;
		my @files = ();
		while (my $file = readdir(DIR)) {
			push(@files, $file);
		}
		close(DIR);
		# Build a list of potential jobs to execute
		my @potential_jobs = ();
		foreach my $file (@files) {
			next if ($file =~ m/^\./);
			next unless (-d "$directory/$file");
			
			# Check if the job is already running
			my $already_running = 0;
			foreach my $current_proccess (@current_processes) {
				if ($file =~ $current_proccess) {
					$already_running = 1;
					last;
				}
			}
			last if $already_running == 1;
			push(@potential_jobs, $file);
		}
		
		# Determine time of potential jobs
		my %timed_job_hash = ();
		foreach my $file (@potential_jobs) {
			my $modtime = (stat($directory."/".$file))[9];
			# print $file . " - " . $modtime . "\n";
			$timed_job_hash{$modtime} = $file;
		}
		
		# Execute oldest potential jobs first
		my $executed_jobs = 0;
		foreach my $key (sort { $a <=> $b} keys %timed_job_hash) {
			# Move to directory and execute a script
			# print "$file\n";
			my $file = $timed_job_hash{$key};
			
			chdir($directory.$file) or die "Cant chdir to $directory$file $!";
			
			my $output_dir = getcwd();
			# print "Dir :".$output_dir."\n";
			
			# Check if this job has already been started
			my $already_started_job = 0;
			opendir (OUTPUT_DIR, $output_dir) or die $!;
			close(OUTPUT_DIR);
			while (my $output_file = readdir(OUTPUT_DIR)) {
				if ($output_file =~ m/output/) {				
					$already_started_job = 1;
					last;
				}
			}
			if ($already_started_job == 1) {
				chdir File::Spec->updir;
				chdir File::Spec->updir;
				next;
			}
			
			# Job should be clear to start
			print "Dir :".$output_dir."\n";
			my $param_file = $file."_params.txt";
			open PARAMS, "< $param_file" or die "Can't open $param_file : $!";
			my @param_lines = <PARAMS>;
			close PARAMS;
			
			my @params_list = (	"cutoff",	# distance cutoff
						"run-tag",			# give the run a keyword tag
						"min-length",		# set the minimum sequence comparison length
						"specific-splice",	# splice the alignment, format = 1:100 (start:end)
						"coverage",			# only include positions covered by X% of the alignment
						"bsreps",			# bootstrap the clustering arrangement of an alignment
						"threads",			# how many threads to use during the cluster bootstrap
						"shortcut-freq",	# how frequently to check for the k2p shortcut
						"skip-intra-dist",	# skip calculating the intra-cluster pairwise distance
						"skip-nn",			# skip calculating nearest neighbors
						"bootstrap",		# subsample the entire alignment, currently without replacement
						"bootstrap-size",	# how large to subsample
						"pseudo-reps",		# number of times to subsample
						"raxml-trees",		# tree option
						"raxml-bs-reps",	#
						"raxml-search-reps", #
						"exemplar-tree"		# Print a tree for the exemplars
					);
			my %param_hash = ();
			my $run_tag = '';
			foreach my $param (@params_list) {
				my @param_line = grep { $_ =~ m/$param/ } @param_lines;
				$param_line[0] =~ s/\n//g;
				my @param_val = split ("=", $param_line[0]);
				if(defined($param_val[1])) {
					$param_hash{$param_val[0]} = $param_val[1];
					if($param_val[0] eq 'run-tag') {
						$run_tag = '_'.$param_val[1];
					}
				}
			}
			my $param_string = '';
			while (my ($key, $value) = each (%param_hash) ) {
				$param_string .= " -".$key." ".$value;
			}
			
			system("mkdir ".$file."_output");
			system("echo \"<html><pre>\" > ".$file."_output/".$file."_console.txt");
			print "Starting ".$file."\n";
			my $delim_command = $delim_path 
					. " -aln1 ".$file.".txt "
					. " -raxml-abs-path ".$raxml_abs_path
					. " -nw-utils-abs-path ".$nw_utils_abs_path
					. $param_string
					. " >> ".$file."_output/".$file."_console.txt &";
			print $delim_command."\n";
			#~ exit;
			system($delim_command);
			# system("echo \"</pre></html>\" >> ".$file."_status.html");
			$executed_jobs++;
			# Move back up to working directory
			chdir File::Spec->updir;
			chdir File::Spec->updir;
			last if $executed_jobs >= $jobs_to_execute;
		}
	}
}
