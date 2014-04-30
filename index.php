<?php
	include('header.php');
?>
	
	<form action="upload_file.php" method="post" enctype="multipart/form-data">
	Filename:
	<input type="hidden" name="sent" id="sent">
	<input type="file" name="file" id="file"><br>
	Job Name:
	<input type="text" name="run-tag" id="run-tag">
	Distance Cutoff:
	<input type="text" name="cutoff" value=0.02 size=5>
	Coverage:
	<select name="coverage">
		<option value=0>0%</option>
		<option value=0.1>10%</option>
		<option selected="selected" value=0.25>25%</option>
		<option value=0.50>50%</option>
		<option value=0.90>90%</option>
	</select>
	Min Comparison Length:
	<input type="text" name="min-length" value=200 size=5>
	Splice Range:
	<input type="text" name="specific-splice" id="specific-splice">
	Bootstrap Reps:
	<select name="bsreps">
		<option value=0 selected="selected">0</option>
		<option value=10>10</option>
		<option value=25>25</option>
		<option value=50>50</option>
		<option value=100>100</option>
		<option value=500>500</option>
		<option value=1000>1000</option>
	</select>
	<br />
	Nearest Neighbor:
	<select name="skip-nn">
		<option value=0>Yes</option>
		<option value=1 selected="selected">No</option>
	</select>
	Intra-cluster Distance:
	<select name="skip-intra-dist">
		<option value=0>Yes</option>
		<option value=1 selected="selected">No</option>
	</select>
	Subsample?:
	<select name="bootstrap">
		<option value=0 selected="selected">No</option>
		<option value=1>Yes</option>
	</select>
	Subsample Size:
	<input type="text" name="bootstrap-size" id="bootstrap-size">
	Pseudo Reps:
	<select name="pseudo-reps">
		<option value=0 selected="selected">0</option>
		<option value=10>10</option>
		<option value=25>25</option>
		<option value=50>50</option>
		<option value=100>100</option>
		<option value=500>500</option>
		<option value=1000>1000</option>
	</select>
	<br />
	RAxML Options:
	<input type="radio" >No trees
	<input type="radio" value="bootstrap" name="raxml-trees" >RAxML+Bootstrap (100)
	<input type="radio" value="quick" name="raxml-trees" >RAxML Quick (Best ML Tree)
	<input type="radio" value="quick" name="raxml-trees" >RAxML Fast (Testing)
	BS Reps:
	<input type="text" name="raxml-bs-reps" value=100 size=5>
	Searches:
	<input type="text" name="raxml-search-reps" value=20 size=5>
	Exemplar Tree:
	<select name='exemplar-tree'>
		<option value=1>Yes</option>
		<option value=0 selected="selected">No</option>
	</select>
	<br />
	<input type="submit" name="submit" value="Submit">
	</form>
	

	<hr>
	<div class="table_list">
	<table width="1350px">
		<tr>
			<td>Job #</td>
			<td>Job ID</td>
			<td>Tag</td>
			<td>Start Time</td>
			<td>Sequences</td>
			<td>Cutoff</td>
			<td># Taxa</td>
			<td># OTUs</td>
			<td>Splice Start</td>
			<td>Splice End</td>
			<td>OTU LR</td>
			<td>1:1 Ratio</td>
			<td>UOO</td>
			<td>Finish Time</td>
		</tr>
<?php
	
if ($jobs_dir = opendir('jobs/')) {

	$ordered_jobs_array = array();
    while (false !== ($job_folder = readdir($jobs_dir))) {
		// Skip dots
		if(	$job_folder != "." && 
			$job_folder != ".." &&
			$job_folder != "index.php") {
			$jobs_output_folder = 	'jobs/'
					. $job_folder. "/"
					. $job_folder."_output/";

			$job_link = 	"<a href=job_viewer.php?job_id=".$job_folder
							."&level=exemplar"
							. ">" 
							. $job_folder ."</a>";
			# Read job status
			$jobs_status_file = "jobs/"
									. $job_folder . "/"
									. $job_folder . "_output/"
									. $job_folder . "_status.txt";
			$job_sequence_file = "jobs/"
									. $job_folder . "/"
									. $job_folder . ".txt";
			
			$lines = file($jobs_status_file);
			$job_status_time = stat($job_sequence_file);			
			$job_status_arr = array();
			
			foreach ($lines as $line) {
				$param = explode("=", $line);
				$job_status_arr[$param[0]] = $param[1];
				//echo $line . "<br />\n";
				//echo $job_status_arr[$param[0]] . "<br />\n";
			}
			
			$job_status_string = "
				<td>".$job_link."</td>
				<td>".$job_status_arr['run_tag']."</td>
				<td>".$job_status_arr['start_time']."</td>
				<td>".$job_status_arr['sequences']."</td>
				<td>".($job_status_arr['cutoff']*100)."%</td>
				<td>".$job_status_arr['num_morpho_names']."</td>
				<td>".$job_status_arr['num_otu']."</td>
				<td>".$job_status_arr['splice_start']."</td>
				<td>".$job_status_arr['splice_end']."</td>
				<td>".$job_status_arr['lumping_rate']."</td>
				<td>".$job_status_arr['one_to_one_ratio']."</td>
				<td>".$job_status_arr['used_only_once_ratio']."</td>
				<td>".$job_status_arr['finish_time']."</td>";
			$ordered_jobs_array[$job_status_time['mtime']] = $job_status_string;
		}
    }
    closedir($handle);
}
krsort($ordered_jobs_array);
$job_num = count($ordered_jobs_array);

foreach ($ordered_jobs_array as $time => $string) {
	echo "<tr><td>".$job_num."</td>";
	echo $string;
	$job_num--;
}
	echo "</table></div>";

?>

</body>
</html>

<?php 

// Functions

function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);
	print $files[0];
    return ($files) ? $files : false;
}
?>
