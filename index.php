<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;
if(!$user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Token::check(Input::get('token'))) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'username' => array('required' => true),
                'password' => array('required' => true)
            ));
            if ($validate->passed()) {
                $login = $user->loginUser(Input::get('username'), Input::get('password'), 'staff');
                if ($login) {
                    $lastLogin = $override->get('staff','id',$user->data()->id);
                    if($lastLogin[0]['last_login'] == date('Y-m-d')){}else{
                        $user->updateRecord('staff',array(
                            'last_login' => date('Y-m-d'),
                        ),$user->data()->id);
                    }
                    switch($user->data()->access_level){
                        case 1:
                            Redirect::to('admin.php');
                            break;
                        case 2:
                            Redirect::to('doctor.php');
                            break;
                        case 3:
                            Redirect::to('reception.php');
                            break;
                        case 4:
                            Redirect::to('dashboard.php');
                            break;
                        case 6:
                            Redirect::to('data.php');
                            break;
                        case 7:
                            Redirect::to('form.php?id=12');
                            break;
                    }
                } else {
                    $errorMessage = 'Wrong username or password';
                }
            } else {
                $pageError = $validate->errors();
            }
        }
    }
}else{
    switch($user->data()->access_level){
        case 1:
            Redirect::to('admin.php');
            break;
        case 2:
            Redirect::to('doctor.php');
            break;
        case 3:
            Redirect::to('reception.php');
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="body-full-height">
    
<head>
        <!-- META SECTION -->
        <title> Siha Optical | Login </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <!-- END META SECTION -->
        
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                                     
    </head>
    <body>
        
        <div class="login-container">
        
            <div class="login-box animated fadeInDown">
                <div class="login-logo"></div>
                <?php if($successMessage){?>
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong>Well done!&nbsp;</strong> <?=$successMessage?>
                    </div>
                <?php }elseif($errorMessage){?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong>Oops Error!&nbsp;</strong> <?=$errorMessage?>
                    </div>
                <?php }elseif($pageError){?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong>Oops Error!&nbsp;</strong> <?php foreach($pageError as $error){echo $error.' , ';}?>
                    </div>
                <?php }?>
                <div class="login-body">
                    <div class="login-title"><strong>Welcome</strong>, Please login</div>
                    <form class="form-horizontal" method="post">
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" name="username" class="form-control" placeholder="Username" required=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="password" name="password" class="form-control" placeholder="Password" required=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right col-md-6">
                            <input type="hidden" name="token" value="<?=Token::generate();?>">
                            <input type="submit" value="Log In" class="btn btn-info btn-block">
                        </div>
                    </div>
                    </form>
                </div>
                <div class="login-footer">
                    <div class="pull-left">
                        &copy; 2018 Siha Optical Eye Center
                    </div>
                    <div class="pull-right">
                        <a href="#">About</a> |
                        <a href="#">Privacy</a> |
                        <a href="#">Contact Us</a>
                    </div>
                </div>
            </div>
            
        </div>
        <script>
            pageLoadingFrame("show");
            window.onload = function () {
                setTimeout(function(){
                    pageLoadingFrame("hide");
                },100);
            }
        </script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','../../../../www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-36783416-1', 'auto');
        ga('send', 'pageview');
    </script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter25836617 = new Ya.Metrika({
                        id:25836617,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true,
                        webvisor:true
                    });
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = "../../../../mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/25836617" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->    
    </body>
</html>






