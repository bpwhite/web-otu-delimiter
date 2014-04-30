<?php
	include('header.php');
	
	$job_id = $_GET['job_id'];
	echo "Current Job ID: ".$job_id."<br />";
	$jobs_output_folder = 	'jobs/'
			. $job_id. "/"
			. $job_id."_output/";
	
	
if($_GET['level'] == 'exemplar') {
	$job_list_link = "<a href=index.php>Jobs</a> > ".$job_id." > ";
	echo $job_list_link;
	
	$job_lines = file($jobs_output_folder
					. $job_id . "_status.txt");
	$job_status_arr = array();
	
	foreach ($job_lines as $job_line) {
		$job_param = explode("=", $job_line);
		$job_status_arr[$job_param[0]] = $job_param[1];
	}
	
	$current_otu_count = 0;
	if(isset($job_status_arr['num_otu'])) {
		$current_otu_count = $job_status_arr['num_otu'];
	} else {
		$current_otu_count = $job_status_arr['predicted_otu'];
	}
	?>
	
	<table>
	<tr>
		<td><?php echo $job_status_arr['run_tag']; 		?></td>
		<td><?php echo $job_status_arr['start_time']; 	?></td>
		<td><?php echo $job_status_arr['sequences']; 	?></td>
		<td><?php echo ($job_status_arr['cutoff']*100)."%"; ?></td>
		<td><?php echo $job_status_arr['num_morpho_names'];		?></td>
		<td><?php echo $job_status_arr['predicted_otu'];		?></td>	
		<td><?php echo $job_status_arr['num_otu'];		?></td>
		<td><?php echo $job_status_arr['splice_start']; ?></td>
		<td><?php echo $job_status_arr['splice_end']; 	?></td>
		<td><?php echo $job_status_arr['lumping_rate']; 	?></td>
		<td><?php echo $job_status_arr['one_to_one_ratio']; 	?></td>
		<td><?php echo $job_status_arr['used_only_once_ratio']; 	?></td>
		<td><?php echo $job_status_arr['finish_time']; 	?></td>				
	</tr>
	</table>
	<div style=float:left;width:45%>
	<?php
	if(isset($job_status_arr['num_otu'])) {
		echo "<img src=\"".$jobs_output_folder."/".$job_id."_exemplars.tre.png\">";
	} else {
		echo "<bold><h2>Tree loading...</h2><bold><br />";
	}
	echo "<br />";
	echo "<br />";
	?>
	</div>
	
	<div style=float:right;width:50% class="table_list">
	<table border="1" width=800px>
		<tr>
			<td>OTU #</td>
			<td>OTU ID</td>
			<td>BS %</td>
			<td>Morpho #</td>
			<td>Abundance</td>
		</tr>
	<?php

	for($otu_i = 1; $otu_i <=  $current_otu_count; $otu_i++) {
		$otu_prefix = $jobs_output_folder.$job_id."_".$otu_i."/".$job_id;
		$otu_stats_file = $otu_prefix."_".$otu_i."_stats.txt";
		$otu_lines = file($otu_stats_file);
		$otu_stats_arr = array();
		foreach ($otu_lines as $otu_line) {
			$otu_param = explode("=", $otu_line);
			$otu_stats_arr[$otu_param[0]] = $otu_param[1];
		}
		?>
			<tr>
				<td><?php echo "<a href=job_viewer.php?job_id=".$job_id."&level=otu&otu_num=".$otu_i.">OTU ".$otu_i."</a><br />"; ?></td>
				<td><?php echo $otu_stats_arr['otu_id']; 					?></td>
				<td><?php echo $otu_stats_arr['bootstrap_percentage']; 		?></td>
				<td><?php echo $otu_stats_arr['num_tax']; 					?></td>
				<td><?php echo $otu_stats_arr['abundance']; 				?></td>
			</tr>
		<?php
	}
		?>
		</table>
		</div>
		<?php
} else if($_GET['level'] == 'otu') {
##############################################################################
# OTU VIEW
	$otu_num = $_GET['otu_num'];
	$job_list_link = "<a href=index.php>Jobs</a> > <a href=job_viewer.php?job_id=".$job_id."&level=exemplar>".$job_id."</a> > OTU ".$otu_num;
	echo $job_list_link;
	
	
	$otu_tree_svg = $jobs_output_folder.$job_id."_".$otu_num."/".$job_id."_".$otu_num.".tre.png";
	
	$job_lines = file($jobs_output_folder
					. $job_id . "_status.txt");
	$job_status_arr = array();
	
	foreach ($job_lines as $job_line) {
		$job_param = explode("=", $job_line);
		$job_status_arr[$job_param[0]] = $job_param[1];
	}
	
	$current_otu_count = 0;
	if(isset($job_status_arr['num_otu'])) {
		$current_otu_count = $job_status_arr['num_otu'];
	} else {
		$current_otu_count = $job_status_arr['predicted_otu'];
	}
	
	?>
	<div class="table_list">
	<table border="1" width=200px style=float:right;width:45%>
		<tr><td>Stat</td><td>Result</td></tr>

	<?php

	$otu_i = $otu_num;
	$otu_prefix = $jobs_output_folder.$job_id."_".$otu_i."/".$job_id;
	$otu_stats_file = $otu_prefix."_".$otu_i."_stats.txt";
	$otu_lines = file($otu_stats_file);
	$otu_stats_arr = array();
	foreach ($otu_lines as $otu_line) {
		$otu_param = explode("=", $otu_line);
		$otu_stats_arr[$otu_param[0]] = $otu_param[1];
	}
	
	?>
			<tr><td>OTU #</td><td><?php echo $otu_i; ?></td></tr>
			<tr><td>OTU ID</td><td><?php echo $otu_stats_arr['otu_id']; ?></td></tr>
			<tr><td>BS %</td><td><?php echo $otu_stats_arr['bootstrap_percentage']; 		?></td></tr>
			<tr><td>Morpho #</td><td><?php echo $otu_stats_arr['num_tax']; 					?></td></tr>
			<td>Abundance</td><td><?php echo $otu_stats_arr['abundance']; 				?></td></tr>
			<td>Unique</td><td><?php echo $otu_stats_arr['num_unique_alleles'];		?></td></tr>
			<td>Distinct</td><td><?php echo $otu_stats_arr['num_distinct_alleles'];		?></td></tr>
			<td>Mean Dist.</td><td><?php echo $otu_stats_arr['mean_distance']; 			?></td></tr>
			<td>SE Dist</td><td><?php echo $otu_stats_arr['se_distance']; 				?></td></tr>
			<td>Min Dist</td><td><?php echo $otu_stats_arr['min_distance']; 				?></td></tr>
			<td>Max Dist</td><td><?php echo $otu_stats_arr['max_distance']; 				?></td></tr>
			<td>Mean Comparisons</td><td><?php echo $otu_stats_arr['mean_number_comparisons']; 	?></td></tr>
			<td>SE Comparisons</td><td><?php echo $otu_stats_arr['se_number_comparisons']; 	?></td></tr>
			<td>Min Comparisons</td><td><?php echo $otu_stats_arr['min_number_comparisons']; 	?></td></tr>
			<td>Max Comparisons</td><td><?php echo $otu_stats_arr['max_number_comparisons']; 	?></td></tr>
			<td>Mean Seq. Length</td><td><?php echo $otu_stats_arr['mean_sequence_length']; 		?></td></tr>
			<td>SE Seq. Length</td><td><?php echo $otu_stats_arr['se_sequence_length']; 		?></td></tr>
			<td>Min Seq. Length</td><td><?php echo $otu_stats_arr['min_sequence_length']; 		?></td></tr>
			<td>Max Seq. Length</td><td><?php echo $otu_stats_arr['max_sequence_length']; 		?></td></tr>
			<td>Link #</td><td><?php echo $otu_stats_arr['num_links']; 				?></td></tr>
			<td>Link Depth</td><td><?php echo $otu_stats_arr['link_depth']; 				?></td></tr>
			<td>Link Strength</td><td><?php echo $otu_stats_arr['link_strength_string']; 		?></td></tr>
			<td>NN</td><td><?php echo $otu_stats_arr['nn_id']; 					?></td></tr>
			<td>NN Dist</td><td><?php echo $otu_stats_arr['nn_dist']; 					?></td></tr>
		</table>
		</div>

	<?php
	
	if(file_exists($otu_tree_svg)) {
		//~ echo "<img src=\"".$jobs_output_folder."/".$job_id."_exemplars.tre.png\">";
		echo "<div style=float:left;width:45%>";
		echo "<img src=\"".$otu_tree_svg."\">";
		echo "</div>";
	} else {
		echo "<div style=float:left;width:45%>";
		echo "<bold><h2>Tree loading...</h2><bold><br />";
		echo "</div>";
	}
	
	echo "<br />";
	echo "<br />";
# End OTU View
}

include('footer.php');
?>
