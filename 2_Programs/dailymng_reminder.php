<!DOCTYPE html>
<html>
<body>

#!/usr/bin/php5 -q
<?php

function sendmail($mailid , $message , $count)
{

		$to = $mailid;
		$subject = "You have " . $count . "  reminder(s) in this mail. Dated : ". date("Y-M-d");
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
			echo "<br>" . " Reminder for today ". date("Y-M-d"). " successfully sent to your mail address - " . $mailid . "<br>";
		else
			echo "<br>" . " Date : " . date("Y-M-d"). " Some problem in sending reminder mail to - " . $mailid . "<br>";
}

function get_daily_mng_reminder_msgs($row , $snglinemsgcnt)
{
				
				$y=1;
				while($y < $snglinemsgcnt ) 
				{
					$message  = $message . $row[$y] . "<br>";
					$y++;
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
				$message = "Today's ( " .date("M d ") . " ) reminder " . "<br>" . "<br>";
				$row = fgetcsv($file,1000,"|");	
				$snglinemsgcnt = count($row) ;
				while($row[0] != "mail to" && ! feof($file))
				{
			
					if($row[0] == 'daily mng')
					{
						$return_msg = get_daily_mng_reminder_msgs($row,$snglinemsgcnt);
						if ( $return_msg != "" )
						{
							$message = $message . $return_msg;
							$x += $snglinemsgcnt - 1;
							
						}
					}
		
	
					$row = fgetcsv($file,1000,"|");
					$snglinemsgcnt = count($row) ;
				}
				
				if( $x != 0 ) 
				{ 
					sendmail($mailid , $message , $x);
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
