<?php 
include './Lab5Common/Header.php';
include './Lab5Common/Functions.php';
include_once 'EntityClassLib.php';

session_start();
    
if(!isset($_SESSION["student"])){
    header("Location: Login.php");
}

$student = unserialize(serialize($_SESSION["student"]));
$registered = $_SESSION["registeredCourses"];
$TotalWeelyHours = getStudentRegisteredWeeklyHoursByStudent($student);
$selectDeleteItems = $_POST["selectDelete"];
$errorMsg = "";

function OnDeleteBtn(){
    return "confirm('Are you sure you want to delete?')";
}

if(isset($_POST["btnDelete"])){

    if(!isset($selectDeleteItems)){
        $errorMsg = "You must select at least one course to delete";
    }
    else {
        $errorMsg = "";
       }
    
    if($errorMsg == ""){
      if($_POST["btnDelete"] == "Yes"){
          foreach ($selectDeleteItems as $select){
              $semesterStr = substr($select, 0, 3);
              $courseCodeStr = substr($select, 3);
              
              deleteRegistration($student->getUserId(), $courseCodeStr, $semesterStr);   
          }
        $registered = getRegistrationByStudentId($student->getUserId());
        $_SESSION["registeredCourses"] = $registered;
      }
    }
}
?>

<div class="container">
    <h1>Current Registration</h1>
    <p>Hello <strong> <?php echo $student->getName(); ?> </strong>(not you? change user <a href="Logout.php">here</a>), the following are your current registrations</p>
    
    <form method = "POST" action = "<?=$_SERVER['PHP_SELF'];?>">
        <div>
            <p class="text-danger"><?php echo $errorMsg; ?></p>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                      <th scope="col">Year</th>
                      <th scope="col">Term</th>
                      <th scope="col">Course Code</th>
                      <th scope="col">Course Titlte</th>
                      <th scope="col">Hours</th>
                      <th scope="col">Select</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            
            if(count($registered) == 0){
                echo "<tr><td>Current Registration is Empty</td></tr>";
            }
            else {
//                usort($registered, function($a, $b)
//                {
//                    return strcmp($a->semesterCode, $b->semesterCode);
//                });
                
                foreach ($TotalWeelyHours as $thisSemester =>$weeklyHours){
                    foreach($registered as $record)
                    {
                        $record = unserialize(serialize($record));
                        $semesterCode = $record->getSemesterCode();
                        $semester = getSemesterById($semesterCode);
                        $course = getCourseById($record->getCourseCode());
                        if($thisSemester == $semesterCode){
                            echo "<tr>";
                            echo "<td>".$semester->getYear()."</td>";
                            echo "<td>".$semester->getTerm()."</td>";
                            echo "<td>".$course->getCourseCode()."</td>";
                            echo "<td>".$course->getCourseTitle()."</td>";
                            echo "<td>".$course->getWeeklyHours()."</td>";
                            echo "<td><input class='form-check-input checkbox' type='checkbox' name='selectDelete[]' value='".$semesterCode.$course->getCourseCode()."'></td>";
                            echo "</tr>";
                        }   
                    }  
                    echo "<tr><td colspan='6'><strong>Total Weekly Hours:    </strong>"
                           .$weeklyHours
                           . "</td></tr>";
                }
                   
            } 
            ?>        
                </tbody>
            </table>
        </div>
        
        <button type="submit" name="btnDelete" value="Yes" class="btn btn-primary" onClick=" return confirm('Are you sure you want to delete?');">Delete</button>
        <button type="submit" name="btnClear" id="btnClear" value="clear" class="btn btn-primary">Clear</button>

    </form>
    <script type="text/javascript">
            function deleteItems() {
                //var selected = document.getElementsByClassName('checkbox');
                return confirm('Are you sure you want to delete?');
                if(selected != null){
                    alert("sure?");
                }

    </script>
</div>
<?php include './Lab5Common/Footer.php'; ?>