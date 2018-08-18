<?php
	require_once("concert.php");

  function get8x10(){
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.the8x10.com/shows");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$data = curl_exec($ch);
	if(!$data){
		echo curl_error($ch);
	}
	curl_close($ch);
	$data = htmlspecialchars_decode($data);
	$dom->loadHTML($data);
	$dom->validateOnParse = true;
	libxml_use_internal_errors(false);
	$parser = new DomXPath($dom);
	
	//Query to grab all div tags with class = "list alt"
	$expression = './/div[contains(concat(" ", normalize-space(@class), " "), " list alt ")]';
	
	$info = array();
	
	//Loop through div tags with class = "list alt"
	foreach ($parser->evaluate($expression) as $div) {
        $concert = new Concert();
        $concert->setVenue("The 8x10");
        
		$dom->loadHTML($div->ownerDocument->saveXML($div));
		//Grab first column from 8x10, wrapped by <h3>
		$holder = $dom->getElementsByTagName('h3');
		foreach($holder as $node){
			//String containing column one
			$colArr = str_split($node->nodeValue);
			$index = 0;
			
			$day = '';
			while($colArr[$index] != 'y')
				$day = $day.$colArr[$index++];
			//The day of the concert (Ex. Saturday)
			$day = $day.$colArr[$index++];
			
			$month = '';
			//The month of the concert (Ex. February)
			while($colArr[$index] != ' ')
				$month = $month.$colArr[$index++];
			
			$date = '';
			$index++;
			//The date of the concert (Ex. 02)
			for($i = 0; $i < 2; $i++)
				$date = $date.$colArr[$index++];
			
			$time = '';
			//The time of doors opening (Ex. 8:00PM)
			while($colArr[$index] != ' ')
				$time = $time.$colArr[$index++];
			
			$concert->setDay(date("m-d", strtotime($month."-".$date)));
            $concert->setTime('Doors: '.$time);
		}
        
        //Grab concert genre details from column two
        $parser = new DomXPath($dom);
		$expression = './/h2[contains(concat(" ", normalize-space(@class), " "), " category-title")]';
        $holder = $parser->evaluate($expression);
        foreach($holder as $node){
            $concert->setGenres(innerXML($node));
        }
        
		$parser = new DomXPath($dom);
		$expression = './/div[contains(concat(" ", normalize-space(@class), " "), " title")]';
        //Grab artist details from column three
		$holder = $parser->evaluate($expression);
		$atContent = false;
		$content = '';
		foreach($holder as $node){
			//Create array temp to store the string of XML
			$temp = str_split(innerXML($node));
			//Iterate through the array, starting at the 14th position
			for($i = 14; $i < count($temp); $i++){
				//Check if position is at the opening <a> tag before the content
				if($atContent == false && $temp[$i].$temp[$i+1] == '<a'){
					//Navigate to the end of the opening <a> tag
					while($temp[$i] != '>'){
						$i++;
					}
					$atContent = true;
				}
				elseif($atContent == true){
					$content = $content.$temp[$i];
					
				}
			}
            //Seperate content by <br/> tag
            $content = explode("<br/>", $content);
            //Strip html tags from each piece of content in array
            foreach($content as $temp){
                    //Check if temp contains "&", if so trim off that part
                    if(strpos($temp, "&")){
                        $temp = explode("&", $temp);
                        $temp = $temp[0];
                    }
                    //Check if concert->info is set
                    if($concert->getInfo() == ''){
                        $concert->setInfo(trim(strip_tags($temp)));
                    }
                    //Check if concert->headliner is set
                    elseif($concert->getHeadliner() == ''){
                        $concert->setHeadliner(trim(strip_tags($temp)));
                    }
                    //Check if concert->support is set
                    elseif($concert->getSupport() == ''){
                        $concert->setSupport(trim(strip_tags($temp)));
                    }
                    //Add any additional artists to concert->support
                    else $concert->addSupport(trim(strip_tags($temp))); 
            }
            
            
            $parser = new DomXPath($dom);
            $expression = './/li[contains(concat(" ", normalize-space(@class), " "), " buy")]';
            //Grab ticket purchase link from column four
            $holder = $parser->evaluate($expression);
            foreach($holder as $temp){
                $link = new SimpleXMLElement(innerXML($temp));
                $concert->setTickets((string)$link['href']);
            }
            
            $info[] = $concert;
		}
	}

    return $info;
  }
	
	function innerXML($node) {
		$doc = $node->ownerDocument;
		$frag = $doc->createDocumentFragment();
		foreach($node->childNodes as $child){
			$frag->appendChild($child->cloneNode(TRUE)); 
		} 
		return $doc->saveXML($frag); 
	}
?>