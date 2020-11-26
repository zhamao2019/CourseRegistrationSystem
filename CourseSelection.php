<?php 
include './Lab5Common/Header.php';
include './Lab5Common/Functions.php';
include_once 'EntityClassLib.php';

session_start();

// login validation
if(!isset($_SESSION["student"])){
    header("Location: Login.php");
    exit();
}

$student = unserialize(serialize($_SESSION["student"]));
$registrations = getRegistrationByStudentId($student->getUserId());
$registeredCourses = getCoursesByRegistrationStudentId($student->getUserId());
$_SESSION["registeredCourses"] = $registrations;

$selectedCoures = $_POST["select"];
$errorMsg = "";

$TotalWeelyHours = getStudentRegisteredWeeklyHoursByStudent($student);
$_SESSION["totalWeeklyHours"] = $TotalWeelyHours;

//get semester selected value
if(isset($_GET['semester'])&& $_GET['semester'] !="-1"){
    $semesterValue = $_GET['semester'];
} 
else {
    $semesterValue = '-1';
}



// click submit button
if(isset($_POST["btnSubmit"])){
    $semester = $_POST["semester"];
    
    foreach ($selectedCoures as $select){
        $semesterCodeStr = substr($select,0,3);
        $courseCodeStr = substr($select,3);
        $studentId = $student->getUserId();
        
        $thisCourse = getCourseById($courseCodeStr);
        $courseWeeklyHours = $thisCourse->getWeeklyHours();
        
        if(array_key_exists($semesterCodeStr, $TotalWeelyHours)){
            $TotalWeelyHours[$semesterCodeStr] += $courseWeeklyHours;
        }
    }
    
    if($TotalWeelyHours[$semesterValue]>16){
        $errorMsg = "Your selection exceed the max weekly hours";
    }   
    elseif (count($selectedCoures)== 0) {
        $errorMsg = "Your need select at least one course";
    }
    
    if($errorMsg == "") {
        
        foreach ($selectedCoures as $select){
            $semesterCodeStr = substr($select,0,3);
            $courseCodeStr = substr($select,3);
            $studentId = $student->getUserId();
            addRegistration($studentId, $courseCodeStr, $semesterCodeStr);
        }
        
        $_SESSION["semester"] = $semester;
        $semesterValue = $_SESSION["semester"];
        
        
        // save current registered records into session
        $registrations = getRegistrationByStudentId($student->getUserId());
        $_SESSION["registeredCourses"] = $registrations;
        
    }
}

?>

<div class="container">
    <h1>Course Selection</h1>
    
    <p>Welcome<strong> <?php echo $student->getName(); ?> </strong>! (not you change user <a href="Logout.php">here</a>)</p>
    <p>You have registered<strong> <?php if(isset($TotalWeelyHours[$semesterValue])) {echo $TotalWeelyHours[$semesterValue];} else{ echo '0';} ?> </strong>hours for the selected semester.</p>
    <p>You can register<strong> <?php echo 16-$TotalWeelyHours[$semesterValue]; ?> </strong>more hours of course(s) for the semester.</p>
    <p>Please note that the courses you have registered will not be displayed in the list.</p>
    
    <form method = "POST" action = "<?=$_SERVER['PHP_SELF'];?>">
        <div class="col-sm-3 mb-3" style="float:right"> 
            <select class="form-control" name="semester" id="semester" onchange="semesterOnChange()">
                <option value="-1" <?php if(!isset($_GET["semester"])) echo "selected";?>>select term...</option>
                <?php 
                $semesters = getAllSemesters();
                foreach ($semesters as $s){
                    echo "<option value='"; 
                    echo $s->getSemesterCode()."' ";
                    if(isset($_GET['semester']) && $_GET['semester']==$s->getSemesterCode()){
                        echo "selected";
                    }
                    if(isset($_POST['semester']) && $_POST['semester']==$s->getSemesterCode()){ 
                        echo "selected";
                    }
                    echo ">".$s->getYear()." ".$s->getTerm()."</option>";
                }
                
                ?>
            </select>
            
            <script type="text/javascript">          
                function semesterOnChange(){
                    var semesterSelected = document.getElementById("semester").value;
                    window.location.href = "CourseSelection.php?semester=" + semesterSelected; 
                }
            </script>
        </div>
        <p class="text-danger"><?php echo $errorMsg; ?></p>
        
        <div>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                      <th scope="col">Code</th>
                      <th scope="col">Course Title</th>
                      <th scope="col">Hours</th>
                      <th scope="col">Select</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            
            if($semesterValue != "-1"){
                // retrieve coures from database
                //$semester = getSemesterById($semesterValue);
                $semester = getSemesterById($semesterValue);
                $allCourses = getCoursesBySemester($semester);
     
                //get unRegisteredCourses by comparing two object arrays 
                $unRegisteredCourses = array_udiff($allCourses,$registeredCourses,
                    function ($objOne, $objTwo) {
                        return $objOne->getCourseCode() <=> $objTwo->getCourseCode(); 
                    }
                  );
                
                foreach($unRegisteredCourses as $course)
                {
                    echo "<tr>";
                    echo "<td>".$course->getCourseCode()."</td>";
                    echo "<td>".$course->getCourseTitle()."</td>";
                    echo "<td>".$course->getWeeklyHours()."</td>";
                    echo "<td><input class='form-check-input' type='checkbox' name='select[]'"."value='".$semesterValue.$course->getCourseCode()."'";
//                    if($_GET["select"]){
//                            if(in_array('CAD8405', $_POST["select[]"])) {echo 'checked';}
//                        } 
                    echo "></td>";
                    echo "</tr>";
                }
            } 
            ?>        
                </tbody>
            </table>
        </div>
        
        <button type="submit" name="btnSubmit" class="btn btn-primary" <?php if(!isset($_GET["semester"]) || $_GET["semester"] =="-1") echo "disabled"; ?>>Submit</button>
        <button type="submit" name="btnClear" id="btnClear" value="clear" class="btn btn-primary">Clear</button>
    </form>
</div>
<?php include './Lab5Common/Footer.php'; ?>