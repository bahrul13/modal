<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css" /> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <title> Certificate </title>
</head>
<body>

    <?php
    // Connect to database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "modal";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if(isset($_POST['submit'])){
    // Get form data
        $certCode = $_POST['certCode'];
        $name = $_POST["name"];
        $course = $_POST["course"];
        $duration = $_POST["duration"];
        $dateStrt = $_POST["dateStrt"];
        $dateEnd = $_POST["dateEnd"];
        $target_dir = "upload/";
        $image = $_FILES['image']['name'];
        $target_file = $target_dir . $image;
        $uploadOk = true;
        $error_message = "";

        if($dateEnd > $dateStrt) {
            echo "End date is greater than start date.";
        } else {
            echo "Start date is greater than or equal to end date.";
        

        if(file_exists($target_file)) {
            $error_message .= "Sorry, File already exists. ";
            $uploadOk = false;
        }

        if($_FILES['image']['size'] > 6000000) {
            $error_message = "Sorry, your file is to large. ";
            echo '<script>$(document).ready(function(){$("#error-message").html("' . $error_message . '");$("#myModal").modal("show");});</script>';
            $uploadOk = false;
        }

        $allowedTypes = array('pdf');
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes)) {
            $error_message = "Only PDF files are allowed.";
            echo '<script>$(document).ready(function(){$("#error-message").html("' . $error_message . '");$("#myModal").modal("show");});</script>';
            $uploadOk = false;
        }

        if ($uploadOk == false) {
            $error_message = "Your file is not uploaded. ";
            echo "<script>
                    $('#uploadErrorMessage').text('$error_message');
                    $('#uploadErrorModal').modal('show');
                  </script>";
        } elseif(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO certificates (certCode, name, course, duration, dateStrt, dateEnd, image) 
            VALUES ('$certCode', '$name', '$course', '$duration', '$dateStrt', '$dateEnd', '$target_file')";

            if ($conn->query($sql) === TRUE) {
            // Success modal
            echo '
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="successModalLabel">Success!</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Certificate has been created successfully.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>';
        
            echo '<script>
                    $(document).ready(function() {
                        $("#successModal").modal("show");
                    });
                  </script>';
            } else {
                echo '
                <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="errorModalLabel">Error!</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Error: ' . $sql . '<br>' . $conn->error . '</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
            
                echo '<script>
                        $(document).ready(function() {
                            $("#errorModal").modal("show");
                        });
                      </script>';
            }
        }
    }
}

    // Close database connection
    $conn->close();
    ?>

    <!-- Button to trigger the modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#certificateModal">
    Create Certificate
    </button>

    <!-- Modal -->
    <div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="certificateModalLabel">Create Certificate</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="certCode">Certificate Code:</label>
                <input type="text" class="form-control" id="certCode" name="certCode" value="<?php echo generateCertCode(); ?>" readonly> <br>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" requried> <br>
            </div>
            <div class="form-group">
                <label for="course">Course:</label>
                <input type="text" class="form-control" id="course" name="course" required> <br>
            </div>
            <div class="form-group">
                <label for="duration">Duration:</label>
                <input type="text" class="form-control" id="duration" name="duration" required> <br>
            </div>
            <div class="form-group">
                <label for="dateStrt">Date Started:</label>
                <input type="date" class="form-control" id="dateStrt" name="dateStrt" required> <br>
            </div>
            <div class="form-group">
                <label for="dateEnd">Date Ended:</label>
                <input type="date" class="form-control" id="dateEnd" name="dateEnd" required> <br>
            </div>
            <div class="form-group">
                <label for="image">Certificate:</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><br>
             <button class="btn btn-primary btn-block" type="submit" name="submit">Create</button> <br>
            </div>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!-- Check file size Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Sorry Can't Process.</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <p id="error-message"></p>
        </div>
        </div>
    </div>
    </div>
    <!-- $uploadOk Modal -->
    <div class="modal fade" id="uploadErrorModal" tabindex="-1" role="dialog" aria-labelledby="uploadErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="uploadErrorModalLabel">Upload Error</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p id="uploadErrorMessage"></p>
        </div>
        </div>
    </div>
    </div>
    <!-- empty modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p id="errorText"></p>
        </div>
        </div>
    </div>
    </div>
    </body>
</body>
<?php
function generateCertCode() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 7; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}
?>
<script>
 
</script>

</html>