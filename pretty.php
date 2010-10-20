<?php
class PrettyHelper extends AppHelper {
	
	// line break to BR
	function textBlock($text) {
		$text = str_replace(chr(11),'<br />',$text);
		$text = str_replace(chr(13),'<br />',$text);
		return $text;
	}
	
	function microtime($mst) {
		$return = '';
		
		if(!is_numeric($mst))
			$return = '-';
		elseif (round($mst/1000,2) < 0.1)
			$return = '< 0.1s';
		elseif ($mst > 5000)
			$return = round($mst/1000) . 's';
		else
			$return = round($mst/1000,2) . 's';

		return $return;
	}
	
	// thank you: http://www.php.net/manual/en/function.memory-get-usage.php#96280
	function filesize($size) {
		$unit=array('b','kb','mb','gb','tb','pb');
    	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	function removeTrailingSlash($url) {
		return substr($url,strlen($url)-1,1) == '/' ? substr($url,0,strlen($url)-1) : $url;		
	}
	
	function formatHiddenCreditCardNumber($type,$number) {
		return $type == 'Amex' ? 'XXXX-XXXXXX-X' . $number : 'XXXX-XXXX-XXXX-' . $number;
	}
	
	function breakAtWord($str, $length, $minword = 3) {
		$sub = '';
		$len = 0;
		   
		foreach (explode(' ', $str) as $word) {
			$part = (($sub != '') ? ' ' : '') . $word;
			$sub .= $part;
			$len += strlen($part);
			      
			if (strlen($word) > $minword && strlen($sub) >= $length) {
				break;
			}
		}
		
		return $sub . (($len < strlen($str)) ? '...' : '');
	}
	
	// 3 hours ago style; constantly updated
	function JsDtAgo($dt) {
		date_default_timezone_set('America/New_York');
		$display = 'Never';
		
		if(!is_numeric($dt))
			$dt = strtotime($dt);
		
		if(is_numeric($dt)) {
			$dt = date('Y-m-d\TH:i:s\Z',$dt-date('Z',$dt));
			$display = '<span class="ConvertToAgo"><span class="Holder">'.$dt.'</span><span class="Source" style="display:none">'.$dt.'</span></span>';
		}
		
		return $display;
	}
	
	// timezone corret format
	function JsDtNice($dt) {
		date_default_timezone_set('UTC');
		$display = 'Never';
		
		if(!is_numeric($dt))
			$dt = strtotime($dt);
		
		if(is_numeric($dt)) {
			$dt = date('F d, Y H:i:s e',$dt);
			$display = '<span class="ConvertToNice"><span class="Holder">'.$dt.'</span><span class="Source" style="display:none">'.$dt.'</span></span>';
		}
		
		return $display;
	}
	
	function updates($core_updates, $module_updates) {		
		if ($core_updates > 0 && $module_updates > 0)
			$return = $core_updates . ' core ' . $this->plural('update',$core_updates) . ' & ' . $module_updates . ' module ' . $this->plural('update',$module_updates) . ' needed';
		else if($core_updates > 0)
			$return = $core_updates . ' core ' . $this->plural('update',$core_updates);
		else if($module_updates > 0)
			$return = $module_updates . ' module ' . $this->plural('update',$module_updates) . ' needed';
		else
			$return = 'No updates needed';
		
		return $return;
	}
	
    function name($first, $last) {
		$return = "";
		if($first)
			$return = $first." ";
		if($last)
			$return .= $last;
		
		if($return == "")
			$return = "{no name}";
		
        return $this->output($return);
    }
	
	function blankProtection($title,$default='name') {
		return strlen($title) == 0 ? '{no '.$default.'}' : $title;
	}
	
	function d($dt,$format='normal') {
		$format = $format == 'full' ? 'F j, Y' : 'n/j/Y';
		
		if(is_numeric($dt))
			$return=date($format,$dt);
		else if(strtotime($dt))
			$return=date($format,strtotime($dt));
		else
			$return=$dt;
			
		return $this->output($return);
	}
	
	function dt($dt=false,$format='full') {
		$format = $format == 'full' ? 'n/j/Y g:i A T' : 'n/j/Y g:i A';
		
		if(!$dt)
			$return=false;
		else if(is_numeric($dt)) // for seconds sinec epoch thingy
			$return=date($format,$dt);
		else if(strtotime($dt))
			$return=date($format,strtotime($dt));
		else
			$return=$dt;
			
		return $this->output($return);
	}
	
	function dt_ago($dt) {
		$hour_in_seconds = 60*60;
		$day_in_seconds = $hour_in_seconds*24;
		$month_in_seconds = 30*$day_in_seconds;
		$year_in_seconds = 365*$day_in_seconds;
		
		if(strtotime($dt) || is_numeric($dt)) {
			if(is_numeric($dt))
				$diff = time() - $dt;
			else
				$diff = time() - strtotime($dt);

			switch (true) {
				case ($diff < 180):
					$return = 'just now';
					break;
				case ($diff < $hour_in_seconds):
					$return = round($diff/60) . ' ' . $this->plural('minute',round($diff/60)) . ' ago';  
					break;
				case ($diff > $year_in_seconds):
					$return = round($diff/$year_in_seconds) . ' ' . $this->plural('year',round($diff/$year_in_seconds)) . ' ago';
					break;
				case ($diff > $month_in_seconds):
					$return = round($diff/$month_in_seconds) . ' ' . $this->plural('month',round($diff/$month_in_seconds)) . ' ago';
					break;
				case ($diff > $day_in_seconds):
					$return = round($diff/$day_in_seconds) . ' ' . $this->plural('day',round($diff/$day_in_seconds)) . ' ago';
					break;	
				case ($diff > $hour_in_seconds):
					$return = round($diff/$hour_in_seconds) . ' ' . $this->plural('hour',round($diff/$hour_in_seconds)) . ' ago';  
					break;	
				default:
					$return = $this->dt($dt);
					break;
			}
		} else
			$return='Never';
		
		return $this->output($return);
	}
	
	function dt_length($seconds_left) {
		$return = null;
		
		$hour_in_seconds = 60*60;
		
		$hours = 0;
		$minutes = 0;
		
		if($seconds_left > $hour_in_seconds) {
			$hours = round($seconds_left/$hour_in_seconds);
			$seconds_left = $seconds_left % $hour_in_seconds;
		}
		
		if($seconds_left > 0) {
			$minutes = round($seconds_left/60);
		}
		
		if($hours > 0 && $minutes > 0)
			$return = $hours . ' ' . $this->plural('hour',$hours) . ' ' . $minutes . ' ' . $this->plural('minute',$minutes);
		else if($hours > 0)
			$return = $hours . ' ' . $this->plural('hour',$hours);
		else
			$return = $minutes . ' ' . $this->plural('minute',$minutes);
		
		return $this->output($return);
	}
	
	function plural($word,$count) {
		return abs($count)>1 || $count == 0 ? $word.'s' : $word;
	}

	function address($address1, $address2, $city, $state, $postal_code, $country, $break="<br />") {
		$return = $address1;
	
		if($address2)
			$return = "$return$break$address2";
			
		if(!empty($city) || !empty($state) || !empty($post_code) )
			$return = "$return$break$city, $state $postal_code$break$country";
			
		return $this->output($return);
	
	}
	
	function citystate($city,$state) {
		$return = "$city";
		
		if($state)
			$return = "$return, $state";
	
		return $this->output($return);
	}
	
	function username($name = false,$email = false) {
		if(strlen($name) == 0)
			$name = false;
		
		if(strlen($email) == 0)
			$email = false;
		
		if($name && $email)
			$display = "$name ($email)";
		else if ($name)
			$display = $name;
		else if (!$name && !$email)
			$display = '{no name}';
		else
			$display = $email;
			
		return $display;
	}
	
	function yesno($f) {
		return $f==1 ? 'Yes' : $f ? 'Yes' : 'No';
	}
	
	function days($days) {
		return ($days == 1) ? '1 day' : $days . ' days';
	}
	
	function percentage($n) {
		return round(100*$n);
	}
	
	function title($title,$default) {
		return $title == '' ? $default : $title;
	}
	
	// money formatter
	// taken from: http://us.php.net/manual/en/function.number-format.php#87381
	// formats money to a whole number or with 2 decimals; includes a dollar sign in front
	function m($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
		if (is_numeric($number)) { // a number
			if (!$number) { // zero
				$money = ($cents == 2 ? '0.00' : '0'); // output zero
			} else { // value
				if (floor($number) == $number) { // whole number
					$money = number_format($number, ($cents == 2 ? 2 : 0)); // format
				} else { // cents
				$money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
				} // integer or decimal
			} // value
			return '$'.$money;
		} // numeric
	} // formatMoney
	
	function n($number) {
		return number_format($number) ? number_format($number) : false;
	}
	
}
?>