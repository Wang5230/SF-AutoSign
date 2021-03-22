<?php
    include 'Spyc.php';
    function checkname($name){
        if(strlen($name)!=9 || $name[0]!='A'){
            return true; 
        }
        $numser = substr($name, 1, 8);
        if(!is_numeric($numser))return true;
        return false;  
    }
    function send_post($params) {
        $url = 'http://39.97.238.129:8080/wisedu-unified-login-api-v1.0/api/login';
        $postdata = http_build_query($params);
        $options = array(
            'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
          )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    };
    function register(){
        $name = $_POST['name'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        if(checkname($name)){
            echo "<script>alert('❌学号格式有误!')</script>";
            return;
        }
        if(empty($_POST['password'])){
            echo "<script>alert('❌密码不允许留空!')</script>";
            return;
        }
        if(empty($_POST['email'])){
            echo "<script>alert('❌请输入邮箱!')</script>";
            return;
        }

        $yaml = Spyc::YAMLLoad('config.yml');
        $a = array('user' => array(
                'username' => $name,
                'password' => $password,
                'email' => $email,
                'address' => '中国黑龙江省哈尔滨市香坊区明德路',
                'school'=> '东北农业大学',
                'lon' => '126.733936',
                'lat'=> '45.750749',
                'abnormalReason' => '在家'
            )
        );
        $params = array(
            'login_url'=> 'https://neau.campusphere.net/iap/login?service=https%3A%2F%2Fneau.campusphere.net%2Fportal%2Flogin',
            'needcaptcha_url'=> '',
            'captcha_url'=> '',
            'username'=> $name,
            'password'=> $password
        );
        $res = send_post($params);
        $res = json_decode($res,true);
        if($res['msg'] != 'login success!'){
            echo "<script>alert('❌账号或密码错误!')</script>";
            return;
        }
        file_put_contents('messages.txt', '['.date("Y/m/d").']用户: '.$name.PHP_EOL.$message.PHP_EOL, FILE_APPEND);
        foreach($yaml['users'] as $stu){
            if($stu['user']['username']==$name){
                echo "<script>alert('❌当前学号已存在!')</script>";
                return;
            }
        }

        array_push($yaml['users'], $a);
        // $yaml['users']['user']['username'] = '\'' + $name + '\'';
        // $yaml['users']['user']['password'] = '\'' + $password + '\'';
        // $yaml['users']['user']['email'] = '\'' + $email + '\'';
        // $yaml['users']['user']['address'] = '中国黑龙江省哈尔滨市香坊区明德路';
        // $yaml['users']['user']['school'] = '东北农业大学';
        // $yaml['users']['user']['lon'] = '126.733936';
        // $yaml['users']['user']['lat'] = '45.750749';
        // $yaml['users']['user']['abnormalReason'] = '在家';
        $yaml = Spyc::YAMLDump($yaml);

        file_put_contents("config.yml",$yaml);
        echo "<script>alert('✔️提交成功!')</script>";
    }
    // 判断用户是否提交的post请求
    if($_SERVER["REQUEST_METHOD"]==="POST"){
        register();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>FORM Text</title>
    <style>
    .galary {
        border-radius: 10px;
        box-shadow: 0 0 3px 1px rgba(51, 51, 51, 0.5);
        transition: 0.3s;
    }
    .galary:hover {
        box-shadow: 0 3px 15px 3px rgba(51, 51, 51, 0.5);
    }
    body{
        background-color: #888;
    }
    .basic-grey {
        margin-left:auto;
        margin-right:auto;
        max-width: 500px;
        background: rgba(247,247,247,.8);
        padding: 25px 15px 25px 10px;
        font: 15px Georgia, "SimSun", Times, serif;
        color: #888;
        text-shadow: 1px 1px 1px #FFF;
        border: 1px solid #E4E4E4;
    }
    .basic-grey h1 {
        font-size: 25px;
        padding: 0px 0px 10px 40px;
        display: block;
        border-bottom:1px solid #E4E4E4;
        margin: -10px -15px 30px -10px;;
        color: #888;
    }
    .basic-grey h1>span {
        display: block;
        font-size: 11px;
    }
    .basic-grey label {
        display: block;
        margin: 0px;
    }
    .basic-grey label>span {
        float: left;
        width: 20%;
        text-align: right;
        padding-right: 10px;
        margin-top: 10px;
        color: #888;
    }
    .basic-grey input[type="text"], .basic-grey input[type="email"], .basic-grey input[type="password"], .basic-grey textarea, .basic-grey select {
        border: 1px solid #DADADA;
        color: #888;
        height: 38px;
        margin-bottom: 16px;
        margin-right: 6px;
        margin-top: 2px;
        outline: 0 none;
        padding: 3px 3px 3px 5px;
        width: 70%;
        font-size: 12px;
        line-height:15px;
        box-shadow: inset 0px 1px 4px #ECECEC;
        -moz-box-shadow: inset 0px 1px 4px #ECECEC;
        -webkit-box-shadow: inset 0px 1px 4px #ECECEC;
    }
    .basic-grey textarea{
        padding: 5px 3px 3px 5px;
    }
    .basic-grey select {
        background: #FFF url('down-arrow.png') no-repeat right;
        background: #FFF url('down-arrow.png') no-repeat right;
        appearance:none;
        -webkit-appearance:none;
        -moz-appearance: none;
        text-indent: 0.01px;
        text-overflow: '';
        width: 70%;
        height: 35px;
        line-height: 25px;
    }
    .basic-grey textarea{
        height:100px;
    }
    .basic-grey .button {
        background: #E27575;
        border: none;
        padding: 10px 25px 10px 25px;
        color: #FFF;
        box-shadow: 1px 1px 5px #B6B6B6;
        border-radius: 3px;
        text-shadow: 1px 1px 1px #9E3F3F;
        cursor: pointer;
    }
    .basic-grey .button:hover {
        background: #CF7A7A
    }
    </style>
</head>
<body>
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" class="basic-grey galary" enctype="multipart/form-data">
		<h1>Contact Form
			<span>Please fill all the texts in the fields.</span>
		</h1>
		<label>
			<span>账号 :</span>
			<input id="name" type="text" name="name" placeholder="请输入学号" />
		</label>
        <label>
			<span>密码 :</span>
			<input id="password" type="password" name="password" placeholder="请输入今日校园密码" />
		</label>
		<label>
			<span>邮箱 :</span>
			<input id="email" type="email" name="email" placeholder="请输入你的邮箱" />
		</label>

		<label>
			<span>留言 :</span>
			<textarea id="message" name="message" placeholder="在此处书写你遇到的问题"></textarea>
		</label>
		<!-- <label>
			<span>Subject :</span><select name="selection">
				<option value="Job Inquiry">Job Inquiry</option>
				<option value="General Question">General Question</option>
			</select>
		</label> -->
		<label style="text-align:center;">
		    当前使用人数:
            <?php
                $yaml = Spyc::YAMLLoad('config.yml');
                $num = sizeof($yaml['users']);
                $span = " $num</span>";
                echo $span;
            ?>
        </label>
        <br>
        <label>
			<span>&nbsp;</span>
			<input type="submit" class="button" value="Send" />
        </label>
	</form>
    <div class="panel-group" id="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" 
				   href="#collapseOne">
					点击我进行展开，再次点击我进行折叠。第 1 部分
				</a>
			</h4>
		</div>
		<div id="collapseOne" class="panel-collapse collapse in">
			<div class="panel-body">
				Nihil anim keffiyeh helvetica, craft beer labore wes anderson 
				cred nesciunt sapiente ea proident. Ad vegan excepteur butcher 
				vice lomo.
			</div>
		</div>
	</div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" 
                    href="#collapseTwo">
                        点击我进行展开，再次点击我进行折叠。第 2 部分
                    </a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson 
                    cred nesciunt sapiente ea proident. Ad vegan excepteur butcher 
                    vice lomo.
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" 
                    href="#collapseThree">
                        点击我进行展开，再次点击我进行折叠。第 3 部分
                    </a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse">
                <div class="panel-body">
                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson 
                    cred nesciunt sapiente ea proident. Ad vegan excepteur butcher 
                    vice lomo.
                </div>
            </div>
        </div>
    </div>
</body>
</html>