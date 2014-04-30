<?php

$temp = explode(".", $_FILES["file"]["name"]);

$extension = end($temp);
echo $_FILES["file"]["name"]."<br />";

if ($_FILES["file"]["error"] > 0)
{
echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
}
else
{
	/*
	echo "Upload: " . $_FILES["file"]["name"] . "<br>";
	echo "Type: " . $_FILES["file"]["type"] . "<br>";
	echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
	echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
	*/
	$section = file_get_contents($_FILES["file"]["tmp_name"]);
	
	if(mb_detect_encoding($section, 'ASCII', true)) {
		$job_run_tag = '';
		if(isset($_POST['run-tag'])) {
/*
			$job_run_tag = '_'.$_POST['run-tag'];
*/
			$job_run_tag = $_POST['run-tag'].'_';
		}
		$rand_num = rand(100000000, 9999999999);
		$job_name = $job_run_tag . base_convert( $rand_num, 10, 32 );
		$newfile = "jobs/" . $job_name 
					. "/" . $job_name . '.txt';
		mkdir("jobs/" . $job_name, 0777);
		move_uploaded_file(		$_FILES["file"]["tmp_name"],
								$newfile);
		chmod("jobs/" . $job_name, 0777);
		chmod($newfile, 0777);
		
		$job_params_file_name = "jobs/" . $job_name 
								. "/" . $job_name 
								. '_params.txt';
		$job_params_file_handle = fopen($job_params_file_name, 'w')
			or die("can't open file");
		# List the parameters to be used
		$param_arr = array(	"cutoff",
					"run-tag",
					"min-length",
					"specific-splice",
					"coverage",
					"bsreps",
					"threads",
					"shortcut-freq",
					"skip-intra-dist",
					"skip-nn",
					"bootstrap",
					"bootstrap-size",
					"pseudo-reps",
					"raxml-trees",
					"raxml-bs-reps",
					"raxml-search-reps",
					"exemplar-tree"
					);

		foreach ($param_arr as &$param) {
			fwrite($job_params_file_handle, $param."=".$_POST[$param]."\n");
		}
		/*
		fwrite($job_params_file_handle, "cutoff=".$_POST["cutoff"]."\n");
		fwrite($job_params_file_handle, "run_tag=".$_POST["run_tag"]."\n");
		fwrite($job_params_file_handle, "specific_splice=".$_POST["specific_splice"]."\n");
		fwrite($job_params_file_handle, "coverage=".$_POST["coverage"]."\n");
		*/
		fclose($job_params_file_handle);
		
		//move_uploaded_file($_FILES["file"]["tmp_name"],
		//"jobs/" . $_FILES["file"]["name"]);
		//echo "Stored in: " . "jobs/" . $_FILES["file"]["name"];
		header( 'Location: index.php ') ;
	} else {
		
		//header( 'Location: index.php?create_job=' . $_FILES["file"]["name"] ) ;
		echo "failed ASCII detection <br />";
		
	}
  
}

  
?>
