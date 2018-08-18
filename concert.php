<?php
    require_once('vendor/autoload.php');
    use \ForceUTF8\Encoding;

    class Concert{
        var $info = '';
        var $headliner = '';
        var $support = '';
        var $day = '';
        var $time = '';
        var $price = '';
        var $tickets = '';
        var $genres = '';
        var $venue = '';
        
        //Constructor
        function __construct(){}
        
        //Getters
        function getInfo(){
            return $this->info;
        }
        
        function getHeadliner(){
             return $this->headliner;
        }
        
        function getSupport(){
            return $this->support;
        }
        
        function getDay(){
            return $this->day;
        }
        
        function getTime(){
            return $this->time;
        }
        
        function getPrice(){
            return $this->price;
        }
        
        function getTickets(){
            return $this->tickets;
        }
        
        function getGenres(){
            return $this->genres;
        }
        
        function getVenue(){
            return $this->venue;
        }
        
        //Setters
        function setInfo($info){
            $this->info = Encoding::toUTF8($info);
        }
        function setHeadliner($headliner){
            $this->headliner = Encoding::toUTF8($headliner);
        }
        
        function setSupport($support){
            $this->support = Encoding::toUTF8($support);
        }
        
        function setDay($day){
            $this->day = $day;
        }
        
        function setTime($time){
            $this->time = $time;
        }
        
        function setPrice($price){
            $this->price = $price;
        }
        
        function setTickets($tickets){
            $this->tickets = $tickets;
        }
        
        function setGenres($genres){
            $this->genres = $genres;
        }
        
        function setVenue($venue){
            $this->venue = $venue;
        }
        
        //Add artists to support variable
        function addSupport($support){
            $this->support.", ".Encoding::toUTF8($support);
        }
    }
?>