<?php
  require_once('concert.php');
  
 function get930(){
	/*Testing with loaded document
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTMLFile("930.html");
	$parser = new DomXPath($dom);
	*/
	//Testing scraper
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.930.com");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/cert/DSTRootCAX3.crt");
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
	$concerts = [];
	
	//Query to grab all div tags with class = "list alt"
	$expression = './/div[contains(concat(" ", normalize-space(@class), " "), " list-view-item ")]';
	
	//Loop through div tags with class = "list alt"
	$tempDom = new DOMDocument();

    foreach ($parser->evaluate($expression) as $div) {
		$dom->loadHTML($div->ownerDocument->saveXML($div));
		$parser = new DomXPath($dom);
        $concert = new Concert();
        $concert->setVenue("9:30 Club");

		// store promoter/tour info
		$expression = './/h2[contains(concat(" ", normalize-space(@class), " "), " topline-info ")]';
		foreach($parser->evaluate($expression) as $tempXML){
			$tempDom->loadHTML($tempXML->ownerDocument->saveXML($tempXML));
			$holder = $tempDom->getElementsByTagName('h2');
			foreach($holder as $node){
				$concert->setInfo(strip_tags(trim($node->nodeValue)));
			}
		}
		
		// store headliner info
		$expression = './/h1[contains(concat(" ", normalize-space(@class), " "), " headliners summary ")]';
		foreach($parser->evaluate($expression) as $tempXML){
			$tempDom->loadHTML($tempXML->ownerDocument->saveXML($tempXML));
			$holder = $tempDom->getElementsByTagName('h1');
			foreach($holder as $node){
				$concert->setHeadliner(strip_tags(trim($node->nodeValue)));
			}
		}
		
		// store support info
		$expression = './/h2[contains(concat(" ", normalize-space(@class), " "), " supports description ")]';
		foreach($parser->evaluate($expression) as $tempXML){
			$tempDom->loadHTML($tempXML->ownerDocument->saveXML($tempXML));
			$holder = $tempDom->getElementsByTagName('h2');
			foreach($holder as $node){
				$concert->setSupport(strip_tags(trim($node->nodeValue)));
			}
		}
		
		// store date info
		$expression = './/h2[contains(concat(" ", normalize-space(@class), " "), " dates ")]';
		foreach($parser->evaluate($expression) as $tempXML){
			$tempDom->loadHTML($tempXML->ownerDocument->saveXML($tempXML));
			$holder = $tempDom->getElementsByTagName('h2');
			foreach($holder as $node){
                $concert->setDay(date("m-d", strtotime($node->nodeValue)));
			}
		}
		
		// store door time
		$expression = './/h2[contains(concat(" ", normalize-space(@class), " "), " times ")]';
		foreach($parser->evaluate($expression) as $tempXML){
			$tempDom->loadHTML($tempXML->ownerDocument->saveXML($tempXML));
			$holder = $tempDom->getElementsByTagName('h2');
			foreach($holder as $node){
				$concert->setTime(trim($node->nodeValue));
			}
		}
		
		// store ticket price
		$expression = './/h3[contains(concat(" ", normalize-space(@class), " "), " price-range ")]';
		foreach($parser->evaluate($expression) as $tempXML){
			$tempDom->loadHTML($tempXML->ownerDocument->saveXML($tempXML));
			$holder = $tempDom->getElementsByTagName('h3');
			foreach($holder as $node){
				$concert->setPrice(trim($node->nodeValue));
			}
		}
        
        // store ticket link or notify if show is sold out
        $expression = './/h3[contains(concat(" ", normalize-space(@class), " "), " ticket-link ")]';
        $holder = $parser->evaluate($expression);
            foreach($holder as $temp){
                $link = new SimpleXMLElement(innerXML($temp));
                $concert->setTickets((string)$link['href']);
            }
		$concerts[] = $concert;
	}
	return $concerts;
 }
?>