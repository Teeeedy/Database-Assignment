<!DOCTYPE html>
<html lang="en">
<head>
    <script src="../js/get_address.js"></script>
    <link rel="stylesheet" href="../css/form_page.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electoral Role Search</title>
</head>
<body>
    <main>
        <form id="form-box" action="validate_user.php" method="post">
            <div id="form-title">Electoral Role Search</div>

            <div class="form-header">Full Name</div>
            <input type="text" class="form-input" id="full_name" name="full_name" placeholder="Enter your full name here..." required></input>
            
            <div class="form-header">Address</div>
            <input type="text" class="form-input" id="addrs_1" name="address" placeholder="Search address here..." required></input>

            <div class="form-header">Apartment Suite, etc (optional)</div>
            <input type="text" class="form-input" id="apt_suite" name="apt_suite"></input>
            <input type='hidden' value="-1" name="apt_suite">
            
            <div class="form-header">Suburb</div>
            <input type="text" class="form-input" id="suburb" name="suburb" required></input>
            
            <div class="form-header">State</div>
            <input class="form-input" id="state" name="state" required></input>
            
            <div class="form-header">Postcode</div>
            <input class="form-input" id="postcode" name="postcode" required></input>

            <div class="voter_status">Have you voted before in THIS election? (Tick if already voted)</div>
            <input type='hidden' value="0" name="voter_status">
            <input type="checkbox" id="voter_status" name="voter_status" value="1">
          
            <input class="btn" type="submit" name="SEARCH" value="SEARCH">
            
          </form>
    </main>
    

    
</body>
</html>