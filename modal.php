<?php
    // Connect to database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "modal";
    $conn = new mysqli($servername, $username, $password, $dbname);
    // $results_per_page = 2;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // $sql = "SELECT COUNT(*) as total FROM certificates";
    // $result = $conn->query($sql);
    // $row = $result->fetch_assoc();
    // $total_records = $row["total"];

    // // Calculate total number of pages
    // $total_pages = ceil($total_records / $results_per_page);

    // // Get current page number
    // $current_page = isset($_GET["page"]) ? $_GET["page"] : 1;

    // // Calculate the starting record for the current page
    // $start_from = ($current_page - 1) * $results_per_page;

    $sql = "SELECT certCode, name, course, duration, dateStrt, dateEnd, image FROM certificates";
    $result = $conn->query($sql);

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

            if(file_exists($target_file)) {
                $error_message .= "Sorry, File already exists. ";
                $uploadOk = false;
            }

            if($_FILES['image']['size'] > 6000000) {
                $error_message = "Sorry, your file is to large. ";
                echo '<script>$(document).ready(function(){$("#error-message").html("' . $error_message . '");$("#myModal").modal("show");});</script>';
                $uploadOk = false;
            }

            $allowedTypes = array('jpg');
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

    // Close database connection
    $conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css" /> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>

        $(document).ready(function () {
            $('#dtBasicExample').DataTable();
            $('.dataTables_length').addClass('bs-select');
            $('#dtBasicExample_filter input[type="search"]').attr('placeholder','Search by Name');
        });
    </script>
   
    <title> Certificate </title>
</head>
<body>
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
                    <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Cancel</button><br>
                    <button class="btn btn-primary btn-block" type="submit" name="submit">Create</button>
                </div>
            </form>
        </div>

        </div> 
    </div>
    </div>

    <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="th-sm">Certificate Code</th>
                <th class="th-sm">Name</th>
                <th class="th-sm">Course</th>
                <th class="th-sm">Duration</th>
                <th class="th-sm">Date Started</th>
                <th class="th-sm">Date Ended</th>
                <th class="th-sm">Certificate</th>
            </tr>
        </thead><tbody>   
        <?php
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row["certCode"]. "</td>
                    <td>" . $row["name"]. "</td>
                    <td>" . $row["course"]. "</td>
                    <td>" . $row["duration"]. " Hours</td>
                    <td>" . $row["dateStrt"]. "</td>
                    <td>" . $row["dateEnd"]. "</td>
                    <td><a href='" . $row["image"] . "' target='_blank'><img src='" . $row["image"] . "' alt='image' width='20' height='20'></a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }

        // // Display pagination links
        // echo "<div class='pagination'>";
        // for ($i = 1; $i <= $total_pages; $i++) {
        //     if ($i == $current_page) {
        //         echo "<span class='current-page'>$i</span>";
        //     } else {
        //         echo "<a href='?page=$i' style='font-weight: bold; color: black;'>$i</a>";
        //     }
        // }
        // echo "</div>";
        ?>
        </tbody>
        <tfoot>
              <tr>
                <th class="th-sm">Certificate Code</th>
                <th class="th-sm">Name</th>
                <th class="th-sm">Course</th>
                <th class="th-sm">Duration</th>
                <th class="th-sm">Date Started</th>
                <th class="th-sm">Date Ended</th>
                <th class="th-sm">Certificate</th>
              </tr>
        </tfoot>
    </table>

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
</html>