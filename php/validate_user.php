<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/ballot_page.css">
    </head>
</html>


<?php


// establish a database connection to your Oracle database.
$username = 's3963096';
$password = 'Ok!6C9j2';
$servername = 'talsprddb01.int.its.rmit.edu.au';
$servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
$connection = $servername . "/" . $servicename;

$conn = oci_connect($username, $password, $connection);
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
} else {
    // Get if use ticked voted "1" or not voted "0"

    $voted_status = $_POST["voter_status"];

    if ($voted_status == "1") {
        echo "<div class=\"voted_container\">";
        echo "<p> You have already voted in the election. You cannot vote again. </p>";
        echo "<a href=\"index.php\"><button class=\"btn\">Back</button></a>";
        echo "</div>";
    } else {
        // Gtting the full name 
        // And then splitting it to get Fname Mname Lname
        $fullname = $_POST["full_name"];

        $fullnamearray = explode(" ", $fullname);

        if (count($fullnamearray) > 2) {
            $fn = '%' . $fullnamearray[0] . '%';
            $mn = '%' . $fullnamearray[1] . '%';
            $ln = '%' . $fullnamearray[2] . '%';

            $mn = $strtolower($mn);
            oci_bind_by_name($stid, ":mn_bv", $mn);
        } else {
            $fn = '%' . $fullnamearray[0] . '%';
            $ln = '%' . $fullnamearray[1] . '%';
        }

        // Getting the full address and splitting it to get street no. and street name
        $add = $_POST["address"];
        $addressarray = explode(" ", $add);
        $stno = '%' . $addressarray[0] . '%';
        $stname = '%' . $addressarray[1] . '%';


        // Getting the rest of the fields required
        $apt = '%' . $_POST["apt_suite"] . '%';
        $sub = '%' . $_POST["suburb"] . '%';
        $state = '%' . $_POST["state"] . '%';
        $pc = '%' . $_POST["postcode"] . '%';

        // Converting all the strings to lowercase
        $fn = strtolower($fn);
        $ln = strtolower($ln);
        $add = strtolower($add);
        $apt = strtolower($apt);
        $sub = strtolower($sub);
        $state = strtolower($state);
        $pc = strtolower($pc);
        $stno = strtolower($stno);
        $stname = strtolower($stname);


        $query = "SELECT VOTERID
                  FROM VOTERREGISTRY
                  WHERE LOWER (firstname) LIKE :fn_bv AND
                        LOWER (lastname) LIKE :ln_bv AND
                        LOWER (addressstreetno) LIKE :stno_bv AND
                        LOWER (addressstreetname) LIKE :stname_bv AND
                        LOWER (addresssuburb) LIKE :sub_bv AND
                        LOWER (addressstate) LIKE :state_bv AND
                        LOWER (addresspostcode) LIKE :pc_bv AND
                        LOWER (addressunitno) LIKE :apt_bv";

        $stid = oci_parse($conn, $query);

        oci_bind_by_name($stid, ":fn_bv", $fn);
        oci_bind_by_name($stid, ":ln_bv", $ln);
        oci_bind_by_name($stid, ":stno_bv", $stno);
        oci_bind_by_name($stid, ":stname_bv", $stname);
        oci_bind_by_name($stid, ":apt_bv", $apt);
        oci_bind_by_name($stid, ":sub_bv", $sub);
        oci_bind_by_name($stid, ":state_bv", $state);
        oci_bind_by_name($stid, ":pc_bv", $pc);

        oci_execute($stid);

        $result = oci_fetch_array($stid);

        if (!$result) {
            echo "<div class=\"voted_container\">";
            echo "<p> You are not registered in the voter registry.</p>";
            echo "<a href=\"index.php\"><button class=\"btn\">Back</button></a>";
            echo "</div>";
            return;
        }

        $voter_id = $result[0];

        $query = "SELECT COUNT(*)
                  FROM VOTERREGISTRY vr JOIN VOTED v ON vr.voterid = v.voterregistry_voterid
                  WHERE LOWER (firstname) LIKE :fn_bv AND
                        LOWER (lastname) LIKE :ln_bv AND
                        LOWER (addressstreetno) LIKE :stno_bv AND
                        LOWER (addressstreetname) LIKE :stname_bv AND
                        LOWER (addresssuburb) LIKE :sub_bv AND
                        LOWER (addressstate) LIKE :state_bv AND
                        LOWER (addresspostcode) LIKE :pc_bv AND
                        LOWER (addressunitno) LIKE :apt_bv";




        $stid = oci_parse($conn, $query);

        // Binding all the php variables to Oracle bind variables
        oci_bind_by_name($stid, ":fn_bv", $fn);
        oci_bind_by_name($stid, ":ln_bv", $ln);
        oci_bind_by_name($stid, ":stno_bv", $stno);
        oci_bind_by_name($stid, ":stname_bv", $stname);
        oci_bind_by_name($stid, ":apt_bv", $apt);
        oci_bind_by_name($stid, ":sub_bv", $sub);
        oci_bind_by_name($stid, ":state_bv", $state);
        oci_bind_by_name($stid, ":pc_bv", $pc);


        oci_execute($stid);

        $result = oci_fetch_array($stid);

        if ($result[0] > 0) {
            echo "<div class=\"voted_container\">";
            echo "<p> You have already previously voted. Voting again is a criminal offence.</p>";
            echo "<a href=\"index.php\"><button class=\"btn\">Back</button></a>";
            echo "</div>";
        } else {




            $query2 = "SELECT ELECTORATE_ELECTORATENAME
                  FROM VOTERREGISTRY
                  WHERE LOWER (firstname) LIKE :fn_bv AND
                        LOWER (lastname) LIKE :ln_bv AND
                        LOWER (addressstreetno) LIKE :stno_bv AND
                        LOWER (addressstreetname) LIKE :stname_bv AND
                        LOWER (addresssuburb) LIKE :sub_bv AND
                        LOWER (addressstate) LIKE :state_bv AND
                        LOWER (addresspostcode) LIKE :pc_bv AND
                        LOWER (addressunitno) LIKE :apt_bv";

            $stid2 = oci_parse($conn, $query2);


            oci_bind_by_name($stid2, ":fn_bv", $fn);
            oci_bind_by_name($stid2, ":ln_bv", $ln);
            oci_bind_by_name($stid2, ":stno_bv", $stno);
            oci_bind_by_name($stid2, ":stname_bv", $stname);
            oci_bind_by_name($stid2, ":apt_bv", $apt);
            oci_bind_by_name($stid2, ":sub_bv", $sub);
            oci_bind_by_name($stid2, ":state_bv", $state);
            oci_bind_by_name($stid2, ":pc_bv", $pc);


            oci_execute($stid2);
            $result = oci_fetch_array($stid2);
            $electorate = $result[0];


            $query3 = "SELECT CANDIDATENAME, POLITICALPARTY_PARTYCODE, LOGOURL, CANDIDATEID
        FROM CANDIDATE JOIN POLITICALPARTY ON CANDIDATE.POLITICALPARTY_PARTYCODE = POLITICALPARTY.PARTYCODE
        WHERE CANDIDATE.ELECTORATE_ELECTORATENAME = :electorate_bv";

            $stid3 = oci_parse($conn, $query3);

            oci_bind_by_name($stid3, ":electorate_bv", $electorate);

            oci_execute($stid3);

            echo "<form id=\"candidate-form-box\" action=\"issue_ballot.php\" method=\"post\">";

            echo "<div class=\"form_header_container\">";
            echo "<div class=\"form_header\">Victoria</div>";
            echo "<div class=\"form_header\">Electoral Divison of $electorate</div>";
            echo "</div>";
            echo "<div class=\"instructions\">Number the boxes starting from 1 in the order of your choice.</div>";
            while ($result = oci_fetch_array($stid3, OCI_NUM + OCI_RETURN_NULLS)) {
                $hashed_value = hash('sha256', $result[0]);

                echo "<div class=\"candidate-row\">";
                echo "<div class=\"image_container\">";
                echo "<img src=$result[2]>";
                echo "</div>";
                echo "<input type=\"text\" class=\"form_input\" name=\"$result[3]\"></input>";
                echo "<div class=\"name_and_party\">";
                echo "<div>$result[0]</div>";
                echo "<div>$result[1]</div>";
                echo "</div>";
                echo "</div>";
            }
            echo "<a href=\"index.php\"><input class=\"btn\" type=\"submit\" name=\"SUBMIT\" value=\"SUBMIT\"></a>";
            echo "<input type='hidden' value='$electorate' name=\"electorate\">";
            echo "<input type='hidden' value='$voter_id' name=\"voter_id\">";
            echo "</form>";
        }
    }
}    
    
oci_close($conn);
    



        