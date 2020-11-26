<?php

class User {
    private $id;
    private $name;
    private $phone;
    
    public  $totalWeeklyHours;
    private $messages;
    
    public function __construct($id, $name, $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        
        $this->messages = array();
        $this->totalWeeklyHours = array();
    }

    public function getUserId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPhone() {
        return $this->phone;
    }
    
    public function getTotalWeeklyHours() {
        return $this->totalWeeklyHours;
    }
    
    public function setTotalWeeklyHours($arr) {
        $this->totalWeeklyHours = $arr;
    }
}

class Course {
    private $courseCode;
    private  $title;
    private  $weeklyHours;
    
     public function __construct($courseCode, $title, $weeklyHours) {
        $this->courseCode = $courseCode;
        $this->title = $title;
        $this->weeklyHours = $weeklyHours;
    }
    
    public function getCourseCode() {
        return $this->courseCode;
    }

    public function getCourseTitle() {
        return $this->title;
    }

    public function getWeeklyHours() {
        return $this->weeklyHours;
    }
}


class Semester {
    private  $semesterCode;
    private  $term;
    private  $year;

    public function __construct($semesterCode, $term, $year) {
        $this->semesterCode = $semesterCode;
        $this->term = $term;
        $this->year = $year;
    }
        
    public function getSemesterCode() {
        return $this->semesterCode;
    }
    
    public function getTerm() {
        return $this->term;
    }
    
    public function getYear() {
        return $this->year;
    } 
}

class CourseOffer {
    private  $courseCode;
    private  $semesterCode;
    
    public function __construct($courseCode, $semesterCode) {
        $this->courseCode = $courseCode;
        $this->semesterCode = $semesterCode;
    }
}

Class Registration {
    private  $userId;
    private  $courseCode;
    private  $semesterCode;

    public function __construct($userId, $courseCode, $semesterCode) {
        $this->userId = $userId;
        $this->courseCode = $courseCode;
        $this->semesterCode = $semesterCode;
    }
          
    public function getUserId() {
        return $this->userId;
    }
    
    public function getCourseCode() {
        return $this->courseCode;
    }
    
    public function getSemesterCode() {
        return $this->semesterCode;
    } 
    

}
