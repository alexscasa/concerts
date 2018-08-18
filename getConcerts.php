<?php
    require_once('930_scrapper.php');
    require_once('8x10_scrapper.php');

    $concerts = array();
    $input = $_GET['data'];
    if(strpos($input, "930")){
        $concerts = addConcerts($concerts, get930());
    }

    if(strpos($input, "8x10")){
        $concerts = addConcerts($concerts, get8x10());
    }

    usort($concerts, "sortConcertDates");

    //json_encode($concerts);

    /*$temp = get930();
    $concerts = addConcerts($concerts, $temp);
    $temp = get8x10();
    $concerts = addConcerts($concerts, $temp);*/

    $formatted = '';

    // for each concert, create a bootstrap panel
    foreach($concerts as $concert){
            $formatted .= '<div class="col-md-3 concert-col concert">';
            $formatted .= '<div class="panel panel-default">';
            $formatted .= '<div class="panel-heading">';
            $formatted .= '<h4 class="panel-title concert-title">';
            $formatted .= ''.$concert->getDay().' at '.$concert->getVenue();
            $formatted .= '</h4></div>';
            $formatted .= '<div class="panel-body content">';
            if($concert->getInfo() != ''){
                $formatted .= ''.$concert->getInfo().'<br/>';
            }
            $formatted .= ''.$concert->getHeadliner().'<br/>';
            if($concert->getSupport() != ''){
                $formatted .= ''.$concert->getSupport().'<br/>';
            }
            $formatted .= ''.$concert->getTime().'<br/>';
            if($concert->getTickets() != ''){
                $formatted .= '<a href="'.$concert->getTickets().'">Buy Tickets!</a>';
            }
            else $formatted .= 'Tickets Unavailable!';
            $formatted .= '</div></div>';
            $formatted .= '</div>';
    }
    // return concert list
    echo $formatted;

    function sortConcertDates($a, $b){
        // Lazy sort
        if(strpos($a->getDay(), "12-") !== false && strpos($b->getDay(), "01-") !== false)
            return -1;
        elseif(strpos($b->getDay(), "12-") !== false && strpos($a->getDay(), "01-") !== false)
            return 1;
        if(strpos($a->getDay(), "12-") !== false && strpos($b->getDay(), "02-") !== false)
            return -1;
        elseif(strpos($b->getDay(), "12-") !== false && strpos($a->getDay(), "02-") !== false)
            return 1;
        // If a < b, return <0
        //    a > b, return >0
        //    a == b, return 0
        else return strcmp($a->getDay(), $b->getDay());
    }

    // concat multiple concert lists
    function addConcerts($concerts, $temp){
        foreach($temp as $concert){
            $concerts[] = $concert;
        }
        return $concerts;
    }

    // figure out the encoding issue?
    function formatConcert($concerts){
        
    }
?>