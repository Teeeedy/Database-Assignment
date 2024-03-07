<?php
// establish a database connection to your Oracle database.
$username = 's3963096';
$password = 'Ok!6C9j2';
$servername = 'talsprddb01.int.its.rmit.edu.au';
$servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
$connection = $servername."/".$servicename;

$conn = oci_connect($username, $password, $connection);
if(!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
else {
    echo "<div class=\"voted_container\">";
    echo "<p> Thank you for voting. Click below to return to main page. </p>";
    echo "<a href=\"index.php\"><button class=\"btn\">Back</button></a>";
    echo "</div>";
    
    $electorate = $_POST["electorate"];  
    $voter_id = $_POST["voter_id"];

    $query = "SELECT CANDIDATENAME, CANDIDATEID
        FROM CANDIDATE JOIN POLITICALPARTY ON CANDIDATE.POLITICALPARTY_PARTYCODE = POLITICALPARTY.PARTYCODE
        WHERE CANDIDATE.ELECTORATE_ELECTORATENAME = :electorate_bv";

    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":electorate_bv", $electorate);
    oci_execute($stid);

    $candidate_array = [];
    $candidateID_array = [];
    $preferences_array = [];
    
    while ($result = oci_fetch_array($stid, OCI_NUM+OCI_RETURN_NULLS)) {
            array_push($candidate_array, $result[0]);
            array_push($candidateID_array, $result[1]);
    }

    for ($i = 0; $i < count($candidateID_array); $i++) {
        $input = $_POST[$candidateID_array[$i]];
        array_push($preferences_array, $input);
    }

    $query = "SELECT COUNT(*) FROM BALLOT";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $result = oci_fetch_array($stid);
    $num_ballot = $result[0] + 1;

    $query = "SELECT ELECTIONEVENTID FROM ELECTIONEVENT WHERE ELECTORATENAME = :electorate_bv";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":electorate_bv", $electorate);

    oci_execute($stid);
    $result = oci_fetch_array($stid);
    $election_event_id = $result[0];

    // Insert into ballot table
    $query = "INSERT INTO BALLOT VALUES(:num_ballot_bv, :electorate_bv, :election_event_id_bv)";
    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":electorate_bv", $electorate);
    oci_bind_by_name($stid, ":num_ballot_bv", $num_ballot);
    oci_bind_by_name($stid, ":election_event_id_bv", $election_event_id);

    oci_execute($stid);

    // Insert ballot preferences table
    $query = "INSERT INTO BALLOTPREFERENCE VALUES(:num_ballot_bv, :candidate_id_bv, :preference_bv)";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":num_ballot_bv", $num_ballot);
    for ($i=0; $i < count($candidate_array); $i++) {
        oci_bind_by_name($stid, ":candidate_id_bv", $candidateID_array[$i]);
        oci_bind_by_name($stid, ":preference_bv", $preferences_array[$i]);
        oci_execute($stid);
    }

    // Insert into voted table
    $time = date("Y-m-d",time());
    $voter_flag = "yes";
    $station = "online";
    $query = "INSERT INTO VOTED VALUES(:election_event_id_bv, :voter_id_bv, :voter_flag_bv, :time_bv, :station_bv)";
    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":election_event_id_bv", $election_event_id);
    oci_bind_by_name($stid, ":voter_id_bv", $voter_id);
    oci_bind_by_name($stid, ":voter_flag_bv", $voter_flag);
    oci_bind_by_name($stid, ":time_bv", $time);
    oci_bind_by_name($stid, ":station_bv", $station);

    oci_execute($stid);

    header("Location:https://titan.csit.rmit.edu.au/~s3963096/dba/asg4/php/index.php");




    






}
oci_close($conn);
?> 