<?php 
    session_start(); 
	
	$db_ip='127.0.0.1';
	$db_un='root';
	$db_pw='ncutm514';
	$db_link = @mysqli_connect($db_ip,$db_un,$db_pw,'num_game');
	#mysqli_query($db_link,'SET CHARACTER SET UTF-8');	//避免產生亂碼
	
	#if (!empty($db_link->connect_error)) {
	#	die('資料庫連線錯誤:' . $db_link->connect_error); 
	#	echo "hi";		// die()：終止程序
	#}
	#else
	#	echo "ya";
	
?>
<!doctype html><!--  猜數字 1A3B  -->
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>猜數字</title>	
	</head>
	<body>
	<h2>規則如下</h2>
	<!--項目符號-->	
		<ul >
			<li>電腦亂數出現4位數字</li>
			<li>數字0~9皆有可能</li>
			<li>A 表示數字位置皆正確</li>
			<li>B 表示數字正確，位置錯誤</li>			
		</ul>

		<form method="POST" action="num_game.php">
			<button type="submit" value="開始" name="go" size="15" >了解規則，開始遊戲</button>				
		</form>		
		
		<br>
		<hr>
		
		<form method="POST" action="num_game.php">
			<button class="w-100 btn btn-lg btn-primary" type="submit" value="刪除" name="de"  size="10" >刪除紀錄</button>				
		</form>
		<!--  重新一局 ( 正解將亂數更新 )  onclick="return confirm('Yes/No')"  -->
		<br>
		<form method="POST" action="num_game.php">
			<label for="num" class="visually-hidden">輸入猜測的數字：</label>
			<input type="text" name="num" id="num" class="form-control" placeholder="請輸入4個數字" required autofocus>
			<button class="w-100 btn btn-lg btn-primary" type="submit" value="答案" name="ok">送出答案</button>				
		</form>  
		
		
<?php
		if(isset($_POST['de']) == "刪除")
		{
			session_destroy();			
			$sql_del_data="DELETE FROM `num_data` WHERE `A_num`=0"; 
			$result_del_data=mysqli_query($db_link,$sql_del_data);	
			
			$sql_del_data="DELETE FROM `num_data` WHERE `A_num`=1"; 
			$result_del_data=mysqli_query($db_link,$sql_del_data);	
			
			$sql_del_data="DELETE FROM `num_data` WHERE `A_num`=2"; 
			$result_del_data=mysqli_query($db_link,$sql_del_data);	
			
			$sql_del_data="DELETE FROM `num_data` WHERE `A_num`=3"; 
			$result_del_data=mysqli_query($db_link,$sql_del_data);	
			
			$sql_del_data="DELETE FROM `num_data` WHERE `A_num`=4"; 
			$result_del_data=mysqli_query($db_link,$sql_del_data);	
		}
		
		if(isset($_POST['go']) == "開始") #使用者 開始遊戲			
		{
			$_SESSION['game'] =  $_POST['go'] ;
		}
		
		if(isset($_SESSION['game'])  <>  null) 
		{
			if(isset($_SESSION['ans']) == null)
			{
				$ans_1 = rand(0,9);	
				
				again:
				$ans_2 = rand(0,9);	
				if ($ans_2 ==$ans_1)
					goto again;
				
				again_num_3:
				$ans_3 = rand(0,9);				
				if ($ans_3 == $ans_1 OR $ans_3 == $ans_2)
					goto again_num_3;
				
				again_num_4:
				$ans_4 = rand(0,9);				
				if ($ans_4 == $ans_1 OR $ans_4 == $ans_2 OR $ans_4 == $ans_3)
					goto again_num_4;
				
				
				$_SESSION['ans'] = $ans_1.$ans_2.$ans_3.$ans_4;
				$_SESSION['ans1'] = $ans_1;
				$_SESSION['ans2'] = $ans_2;
				$_SESSION['ans3'] = $ans_3;
				$_SESSION['ans4'] = $ans_4;
				
				$_SESSION['times'] = 0 ;
			}
			else
			{
				$A = 0;
				$B = 0;		
				
				if( isset($_POST['ok']) == "答案") #
				{
					$_SESSION['times'] = $_SESSION['times'] +1;
					$num = $_POST['num'];			
					$num1 = substr($num,0,1); 	#取第一個字
					$num2 = substr($num,1,1);	#取	第二個字
					$num3 = substr($num,2,1);
					$num4 = substr($num,3,1);			
						
					if ($num1 ==  $_SESSION['ans1'])#位置 數字接皆正確	
						$A = $A +1 ; 
					if ($num1 == $_SESSION['ans2'] OR $num1 ==  $_SESSION['ans3'] OR $num1 ==  $_SESSION['ans4'])
						$B = $B +1 ;	#數字1 正確  位置錯誤
						
					if ($num2 ==  $_SESSION['ans2'])
						$A = $A +1 ;	
					if ($num2 ==  $_SESSION['ans1'] || $num2 == $_SESSION['ans3'] || $num2 ==  $_SESSION['ans4'])
						$B = $B +1 ;	#數字2 正確  位置錯誤				
						
					if ($num3 ==  $_SESSION['ans3'])
						$A = $A +1 ;	
					if ($num3 ==  $_SESSION['ans1'] || $num3 ==  $_SESSION['ans2'] || $num3 ==  $_SESSION['ans4'])
						$B = $B +1 ;	#數字3 正確  位置錯誤	
						
					if ($num4 ==  $_SESSION['ans4'])
						$A = $A +1 ;
					if ($num4 ==  $_SESSION['ans1']|| $num4 ==  $_SESSION['ans2'] || $num4 ==  $_SESSION['ans3'])
						$B = $B +1 ;	#數字4 正確  位置錯誤	
				}
				
					if ($A == 4)
						echo "<BR>恭喜!答案就是". $_SESSION['ans'] . "，共猜了". $_SESSION['times']  . "次";
					else		
						echo "<BR>提示 ". $A . "A ". $B ."B" ;
					
					if(isset($_POST['num']))
					{
						$sql_new_data="INSERT INTO `num_data`(`num`, `A_num`, `B_num`)VALUES ('" .$num ."','" .$A ."','" .$B ."')"; 
						$result_new_data=mysqli_query($db_link,$sql_new_data);	
						
						echo "<tr>";
						echo "<BR><BR><h3><b>紀錄</b>，目前猜了".$_SESSION['times'] ."次</h3>" ;
						echo "</tr>";
					}
					
					$sql_num="SELECT * FROM `num_data` WHERE 1";
					$result_num=mysqli_query($db_link,$sql_num);						
					while($row=$result_num->fetch_assoc())
					{			
						$str_num = (string)$row['num'];
						echo "<p>".$str_num  . "，". $row['A_num']. "A ". $row['B_num'] ."B</p>";		
					}
						echo "<hr>";
						echo "<h4>正確答案". $_SESSION['ans'] ."</h4>";
			}
		}			
?>
	</body>
</html>
