<?php 
include './Lab5Common/Header.php';
include './Lab5Common/Functions.php';
include_once 'EntityClassLib.php'; 
?>

<?php 
    session_start();        
    $id = $_POST["studentId"];
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $rePassword = $_POST["rePassword"];

    $idErrorMsg = "";
    $nameErrorMsg = "";
    $phoneErrorMsg = "";
    $passwordErrorMsg = "";
    $rePasswordErrorMsg = "";
    $validation = false;
    
    if(isset($_POST["btnClear"])){
        $name=$id=$phone=$password="";
        $_POST = array();
    }
    
    if(isset($_POST["btnSubmit"])){
        $idErrorMsg = ValidateId($id);
        $nameErrorMsg = ValidateName($name);
        $phoneErrorMsg = ValidatePhone($phone);
        $passwordErrorMsg = ValidatePassword($password);
        $rePasswordErrorMsg = ValidateRePassword($password, $rePassword);
        
        // validate all the form
        if( $idErrorMsg=="" && $nameErrorMsg=="" && $phoneErrorMsg =="" && $passwordErrorMsg=="" && $rePasswordErrorMsg==""){
            $validation = true;
        }  
  
        if($validation == true){
            try {
                $hashedPassword= hash("sha256", $_POST["password"]);
                addNewUser($id, $name, $phone, $hashedPassword);
                
                $student = getUserByIdAndPassword($id, $hashedPassword);
                $_SESSION["student"] = $student;
                header("Location: CourseSelection.php");
                exit();
            }
            catch (Exception $e)
            {
                die("The system is currently not available, try again later");
            }
        }
    }
?>
<div class="container">
    <h1>Sign up</h1>
    <p>All fields are required</p>
    
    <form method = "POST" action = "<?=$_SERVER['PHP_SELF'];?>">
        <div class="form-group row">
            <label for="studentId" class="col-lg-2">Student ID:</label>
            <div class="col-lg-3">
                <input type="text" class="form-control" name="studentId" id="studentId" value="<?php echo $_POST["studentId"]; ?>"/>
            </div>
            <div class="col-lg-7 text-danger"><?php echo $idErrorMsg ?></div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-lg-2">Name:</label>
            <div class="col-lg-3">
                <input type="text" class="form-control" name="name" id="name" value="<?php echo $_POST["name"]; ?>"/>
            </div>
            <div class="col-lg-7 text-danger"><?php echo $nameErrorMsg ?></div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-lg-2">Phone Number:</label>
            <div class="col-lg-3">
                <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $_POST["phone"]; ?>"/>
            <small id="phoneStyle" class="form-text text-muted">(nnn-nnn-nnnn)</small>
            </div>
            <div class="col-lg-7 text-danger"><?php echo $phoneErrorMsg ?></div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-lg-2">Password:</label>
            <div class="col-lg-3">
                <input type="password" class="form-control" name="password" id="password" value="<?php echo $_POST["password"]; ?>"/>
            </div>
            <div class="col-lg-7 text-danger"><?php echo $passwordErrorMsg ?></div>
        </div>
        <div class="form-group row">
            <label for="rePassword" class="col-lg-2">Password Again:</label>
            <div class="col-lg-3">
                <input type="password" class="form-control" name="rePassword" id="rePassword" value="<?php echo $_POST["rePassword"]; ?>"/>
            </div>
            <div class="col-lg-7 text-danger"><?php echo $rePasswordErrorMsg ?></div>
        </div>
        
        <button type="submit" name="btnSubmit" class="btn btn-primary">Submit</button>
        <button type="submit" name="btnClear" id="btnClear" value="clear" class="btn btn-primary">Clear</button>
    </form>
    
</div>




<?php include './Lab5Common/Footer.php'; ?>