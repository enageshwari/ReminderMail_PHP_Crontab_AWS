<!DOCTYPE html>
<html>
<body>

#!/usr/bin/php5 -q
<?php

function sendmail($mailid , $message , $count , $tmrwdate)
{

		$to = $mailid;
		$subject = "You have " . $count . "  reminder(s) in this mail for tmrw . Dated : ". $tmrwdate;
		$from = "rahulkanna.lab@gmail.com";

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		// $headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
		// $headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
		// $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
		// $headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

		$headers .= "From:" . $from;
		if( mail($to,$subject,$message,$headers) == TRUE ) 
			echo "<br>" . " Reminder for tmrw ". $tmrwdate . " successfully sent to your mail address - " . $mailid . "<br>";
		else
			echo "<br>" . " Date : " . date("Y-M-d"). " Some problem in sending tmrw's reminder mail to - " . $mailid . "<br>";
}

function get_monthly_reminder_msgs($row , $snglinemsgcnt , $tmrwdate)
{
		

			
			if(strlen($row[1]) == 1)
			{
				$row[1] = "0" . $row[1];
			}
			$fdate1 = date("Y") . "-" . date("M") . "-" . $row[1];

			$tmrw = strtotime($tmrwdate);
			$reminder_day = strtotime($fdate1);
	
			if ($reminder_day == $tmrw) 
			{
				$y=2;
				while($y < $snglinemsgcnt ) 
				{
					$message  = $message . $row[$y] . "<br>";
					$y++;
					
				}
				

			}
						
			return $message;

}


function get_yearly_reminder_msgs($row  , $snglinemsgcnt, $tmrwdate)
{
			
			
			$todays_date = date("Y-M-d");
			if(strlen($row[2]) == 1)
			{
				$row[2] = "0" . $row[2];
			}

			$tmrw = strtotime($tmrwdate);

			$fdate1 = date("Y") . "-" . $row[1] . "-" . $row[2];

		
			$reminder_day = strtotime($fdate1);
	
			if ($reminder_day == $tmrw) 
			{
				
				$y=3;
				while($y < $snglinemsgcnt ) 
				{
					$message  = $message . $row[$y] . "<br>";
			
					$y++;
					
				}

			
			}
					
			return $message;
}


function get_specific_date_reminder_msgs($row  , $snglinemsgcnt, $tmrwdate)
{
			

			$todays_date = date("Y-M-d");
			if(strlen($row[2]) == 1)
			{
				$row[2] = "0" . $row[2];
			}

			$tmrw = strtotime($tmrwdate);

			$fdate1 = $row[3] . "-" . $row[1] . "-" . $row[2];
			

			
			$reminder_day = strtotime($fdate1);
	
			if ($reminder_day == $tmrw) 
			{
				$y=4;
				while($y < $snglinemsgcnt ) 
				{
					$message  = $message . $row[$y] . "<br>";
					$y++;
					
				}
				
			}
							

			return $message;
}

function get_mail_addr($row,$snglinemsgcnt)
{
				$x=1;
				while( $x < $snglinemsgcnt)
				{	
					if ( $x == 1 ) 
					{
						$mailid = $row[$x];
					}					
					else	
					{	
						$mailid = $mailid . "," . $row[$x];
					}
					$x++;

				}
				return $mailid;

}

function chkreminderfile()
{

		$file = fopen("reminderinfo.csv","r");


			$row = fgetcsv($file,1000,"|");
			$snglinemsgcnt = count($row) ;
			if($row[0] == "mail to")
			{
				$mailid = get_mail_addr($row,$snglinemsgcnt);
				
			}
			elseif ($mailid == "" and $row[0] == "")
			{
				echo "IIII Warn IIII :  Reminder File empty !!!!<br>";

			}
		
			else
			{
				echo "XXXX Alert XXXX : Mail ID info not present !!!!<br>";	
			}	


		while(! feof($file) && $mailid != "" )
		{
				
				$x=0;	
				$fdate2 = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
				$tmrwdate = date("M d", $fdate2);
				
				$message = "Tomorrow's ( " .$tmrwdate  . " ) reminder " . "<br>" . "<br>";
				$row = fgetcsv($file,1000,"|");	
				$snglinemsgcnt = count($row) ;
				while($row[0] != "mail to" && ! feof($file))
				{
			

					if($row[0] == 'monthly')
					{
						$return_msg = get_monthly_reminder_msgs($row,$snglinemsgcnt, $tmrwdate);
						
						if ( $return_msg != "" )
						{
							$message = $message . $return_msg;
							$x += $snglinemsgcnt - 2;
							
						}
						
					}
		
					if($row[0] == 'yearly')
					{
						$return_msg = get_yearly_reminder_msgs($row,$snglinemsgcnt, $tmrwdate);
						
						if ( $return_msg != "" )
						{
							$message = $message . $return_msg;
							$x += $snglinemsgcnt - 3;
							
						}
					}

					if($row[0] == 'specific date')
					{
					
						$return_msg = get_specific_date_reminder_msgs($row,$snglinemsgcnt, $tmrwdate);
						
						if ( $return_msg != "" )
						{
							$message = $message .  $return_msg;
							$x += $snglinemsgcnt - 4;
							
						}
					}
					
					$row = fgetcsv($file,1000,"|");
					$snglinemsgcnt = count($row) ;
				}
				
				if( $x != 0 ) 
				{ 
					sendmail($mailid , $message , $x, $tmrwdate);
				}
				else
				{
					echo "<br>" . " No reminder set for mail address - <br>" . $mailid . " for today( ".date("Y-M-d"). ")<br>";
				}
				if( ! feof($file) )
				{
					if($row[0] == "mail to")
					{
						$mailid = get_mail_addr($row,$snglinemsgcnt);
				
					}
					
				}
			
		}



		fclose($file);

		
}


chkreminderfile();



?>

</body>
</html>
