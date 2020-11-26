<?php
include_once 'EntityClassLib.php';

function getPDO()
{
    $dbConnection = parse_ini_file("DBConnection.ini");
    extract($dbConnection);
    return new PDO($dsn, $scriptUser, $scriptPassword);  
}

function addNewUser($userId, $name, $phone, $password)
{
    $pdo = getPDO();
     
    $sql = "INSERT INTO Student VALUES( :studentId, :name, :phone, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['studentId' => $userId, 'name' => $name, 'phone' => $phone, 'password' => $password]);
}

function getUserByIdAndPassword($id, $password)
{
    $pdo = getPDO();
    
    $sql = "SELECT StudentId, Name, Phone FROM Student WHERE StudentId = :userId AND Password = :password";
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['userId'=>$id, 'password'=>$password]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
        
    if ($row)
    {       
        return new User($row['StudentId'], $row['Name'], $row['Phone'] );            
    }
    else
    {
        return null;
    }   
}

function getSemesterById($id){
    $pdo = getPDO();

    $sql = "SELECT SemesterCode, Term, Year FROM Semester WHERE SemesterCode = :semesterCode";
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['semesterCode'=>$id]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row){
        return new Semester($row['SemesterCode'], $row['Term'], $row['Year']);
    }
    else{
        return null;
    }
}

function getAllSemesters()
{
    $pdo = getPDO();
    
    $sql = "SELECT SemesterCode, Term, Year FROM Semester";
        
    $resultSet = $pdo->query($sql);
    
    $semesters = array();
    
    foreach($resultSet as $row)
    {
        $semester = new Semester($row['SemesterCode'], $row['Term'], $row['Year']);
        $semesters[] = $semester;           
    }
    return $semesters;
}

// get All courses
function getCourseById($id)
{
    $pdo = getPDO();
    
    $sql = "SELECT CourseCode, Title, WeeklyHours FROM Course WHERE CourseCode = :CourseCode";
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['CourseCode'=>$id]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row){
        return new Course($row['CourseCode'], $row['Title'], $row['WeeklyHours']);
    }
    else{
        return null;
    }
}

function getRegistrationByStudentId($id){
    $registrations = array();
            
    $pdo = getPDO();
    
    $sql = "SELECT Registration.StudentId, Registration.CourseCode, Registration.SemesterCode "
    ."FROM Registration INNER JOIN Student ON Registration.StudentId = Student.StudentId "
    ."WHERE Registration.StudentId = :userId";
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['userId'=>$id]);
    //$row = $pStmt->fetch(PDO::FETCH_ASSOC);
    
    foreach ($pStmt as $row ){
        $registration = new Registration($row['StudentId'], $row['CourseCode'], $row['SemesterCode']);
        $registrations[] = $registration;
    }
    return $registrations;
       
}


function addRegistration($studentId, $courseId, $semesterId)
{
    $pdo = getPDO();
     
    $sql = "INSERT INTO Registration VALUES( :studentId, :courseCode, :semesterCode)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['studentId' => $studentId, 'courseCode' => $courseId, 'semesterCode' => $semesterId]); 
}

function deleteRegistration($studentId, $courseId, $semesterId){
    $pdo = getPDO();
    $sql = "DELETE FROM Registration WHERE Registration.StudentId = :studentId "
            . "AND Registration.CourseCode = :courseCode "
            . "AND Registration.SemesterCode = :semesterCode";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['studentId' => $studentId, 'courseCode' => $courseId, 'semesterCode' => $semesterId]); 
    //$pStmt->commit;
}

function getCoursesByRegistrationStudentId($id){
    $courses = array();
    $pdo = getPDO();
    $sql = "SELECT Course.CourseCode Code, Title,  WeeklyHours "
            ."FROM Course INNER JOIN Registration ON Course.CourseCode = Registration.CourseCode "
            ."WHERE Registration.StudentId = :studentId";
    
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['studentId'=>$id]);      
       
    foreach ($pStmt as $row ){
            $course = new Course( $row['Code'], $row['Title'], $row['WeeklyHours']);
            $courses[] = $course;
        }
    return $courses;
}

function getCoursesBySemester($semester)
{
    $courses = array();
    $pdo = getPDO();
        
    $sql = "SELECT Course.CourseCode Code, Title,  WeeklyHours "
    ."FROM Course INNER JOIN CourseOffer ON Course.CourseCode = CourseOffer.CourseCode "
    ."WHERE CourseOffer.SemesterCode = :semesterCode";
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['semesterCode'=>$semester->getSemesterCode()]);      
       
    foreach ($pStmt as $row ){
            $course = new Course( $row['Code'], $row['Title'], $row['WeeklyHours']);
            $courses[] = $course;
        }
    return $courses;
    
}

// get student total registered weekly hours
function getStudentRegisteredWeeklyHoursByStudent($student){
    $registrations = getRegistrationByStudentId($student->getUserId());
    $TotalWeelyHours = array();
    
    foreach ($registrations as $registration){
        $registeredSemesterCode = getSemesterById($registration->getSemesterCode())->getSemesterCode();

        $registeredCourse = getCourseById($registration->getCourseCode());
        $courseWeeklyHours = $registeredCourse->getWeeklyHours();

        if(array_key_exists($registeredSemesterCode, $TotalWeelyHours)){
            $TotalWeelyHours[$registeredSemesterCode] += $courseWeeklyHours;
        }
         else {
            $TotalWeelyHours[$registeredSemesterCode] = $courseWeeklyHours;
        }

     }
     return $TotalWeelyHours;
}






// if id is blank
function ValidateId($id) {
    $pdo = getPDO();  
    $sql = "SELECT StudentId FROM Student WHERE StudentId = :userId";      
    $pStmt = $pdo ->prepare($sql); 
    $pStmt ->execute(['userId'=>$id]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    
    $idExised = false;
    
    if ($row)
    {
       $idExised = true;
    }
    else
    {
        return null;
    }

    if( !trim($id) ){
        return $errorMsg = 'Student ID can not be blank';
    }
    elseif ($idExised) {
         return $errorMsg = "A student with this ID has already existed";
    }
    else {
        return $errorMsg = "";
    }
}

// if name is blank
function ValidateName($name) {
    if( !trim($name) ){
        return $errorMsg = 'Name can not be blank';
    }
    else {
        return $errorMsg = "";
    }
}

// if phone is blank or incorrect format
function ValidatePhone($phone) {
    // nmm-nmm-mmm, n is not 0 or 1
    $phoneRege = "/^[2-9](\d{2})-[2-9](\d{2})-(\d{4})$/i";

    if( !trim($phone) ){
        return $errorMsg = 'Phone number can not be blank';
    }
    elseif(!preg_match($phoneRege, $phone)) {
        return $errorMsg = "Incorrect Phone Format";
    }
    else {
        return $errorMsg = "";
    }
}
// if email is blank or incorrect format
function ValidatePassword($password) {

    $passwordRege = "^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$^";

    if( !trim($password)){
        return $passwordErrorMsg = 'Password can not be blank';
    }
    elseif(!preg_match($passwordRege, $password)) {
        return $errorMsg = "Password contains should at least 6 characters,one upper case, one lowercase and one digit.";
    }
    else {
        return $errorMsg = "";
    }
}

function ValidateRePassword($password, $re) {

    if( !trim($re)){
        return $passwordErrorMsg = 'Enter your password again to confirm';
    }
    elseif($password != $re){
        return $errorMsg = "Your password are not same";
    }
    else {
        return $errorMsg = "";
    }
}




?>
