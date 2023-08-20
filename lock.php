<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$username = null;$userId=null;
if($user->isLoggedIn()){
    $userId = $user->data()->id;
    $user->logout();
    $redirect = 'lock.php?id='.$userId;
    Redirect::to($redirect);
}else{
    $username = $override->get('staff','id',$_GET['id']);
    if (Input::exists('post')) {
        $validate = new validate();
        $validate = $validate->check($_POST, array(
            'username' => array('required' => true),
            'password' => array('required' => true)
        ));
        if ($validate->passed()) {
            $login = $user->loginUser(Input::get('username'), Input::get('password'), 'staff');
            if ($login) {
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
?>
<!DOCTYPE html>
<html lang="en" class="body-full-height">

<head>        
        <!-- META SECTION -->
        <title> Siha Optical | Lock </title>
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
        
        <div class="lockscreen-container">
            
            <div class="lockscreen-box animated fadeInDown">
                
                <div class="lsb-access">
                    <div class="lsb-box">
                        <div class="fa fa-lock"></div>
                        <div class="user animated fadeIn">
                            <?php $staff=$override->get('staff','id',$_GET['id'])?>
                            <img src="<?php if($staff[0]['picture']){echo$staff[0]['picture'];}else{echo 'assets/images/users/no-image.jpg';}?>" alt=""/>
                            <div class="user_signin animated fadeIn">
                                <div class="fa fa-sign-in"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lsb-form animated fadeInDown">
                    <form  method="post" class="form-horizontal">
                        <div class="form-group sign-in animated fadeInDown">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <span class="fa fa-user"></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Your login"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <span class="fa fa-lock"></span>
                                    </div>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required=""/>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="username" value="<?=$username[0]['employee_ID']?>"/>
                        <input type="submit" class="hidden"/>
                    </form>
                </div>
                
            </div>
            
        </div>
    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->

        <!-- START TEMPLATE -->                
        <script type="text/javascript" src="js/plugins.js"></script>
        <script type="text/javascript" src="js/actions.js"></script>
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
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






