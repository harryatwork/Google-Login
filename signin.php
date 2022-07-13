<?php include("head.php"); ?>

<style>
.social-login {
    padding-bottom:25px;
}
/* Shared */
.loginBtn {
  box-sizing: border-box;
  position: relative;
  /* width: 13em;  - apply for fixed size */
  margin: 1.2em;
  padding: 5px 15px 5px 46px;
  border: none;
  text-align: left;
  line-height: 34px;
  white-space: nowrap;
  border-radius: 0.2em;
  font-size: 16px;
  color: #FFF;
}
.loginBtn:before {
  content: "";
  box-sizing: border-box;
  position: absolute;
  top: 0;
  left: 0;
  width: 34px;
  height: 100%;
}
.loginBtn:focus {
  outline: none;
  color: #FFF;
}
.loginBtn:active {
  box-shadow: inset 0 0 0 32px rgba(0,0,0,0.1);
  color: #FFF;
}


/* Facebook */
.loginBtn--facebook {
  background-color: #4C69BA;
  background-image: linear-gradient(#4C69BA, #3B55A0);
  /*font-family: "Helvetica neue", Helvetica Neue, Helvetica, Arial, sans-serif;*/
  text-shadow: 0 -1px 0 #354C8C;
}
.loginBtn--facebook:before {
  border-right: #364e92 1px solid;
  background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/14082/icon_facebook.png') 6px 6px no-repeat;
}
.loginBtn--facebook:hover,
.loginBtn--facebook:focus {
  background-color: #5B7BD5;
  background-image: linear-gradient(#5B7BD5, #4864B1);
}


/* Google */
.loginBtn--google {
  /*font-family: "Roboto", Roboto, arial, sans-serif;*/
  background: #DD4B39;
}
.loginBtn--google:before {
  border-right: #BB3F30 1px solid;
  background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/14082/icon_google.png') 6px 6px no-repeat;
}
.loginBtn--google:hover,
.loginBtn--google:focus {
  background: #E74B37;
}
</style>

<body >
   <div id="shopify-section-header-template" class="shopify-section">
      
     <?php include("header.php"); ?> 
      
   </div>
   
   <div class="tt-breadcrumb">
      <div class="container">
        <ul>
          <li><a href="index">Home</a></li>
          <li>Sign In</li></ul>
      </div>
    </div>
   
   
<?php
//Include GP config file && User class
include_once 'g-login/gpConfig.php';
include_once 'g-login/User.php';

if(isset($_GET['code'])){
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
	//Get user profile data from google
	$gpUserProfile = $google_oauthV2->userinfo->get();
	
	//Initialize User class
	$user = new User();
	
	//Insert or update user data to the database
    $gpUserData = array(
        'oauth_provider'=> 'google',
        'oauth_uid'     => $gpUserProfile['id'],
        'first_name'    => $gpUserProfile['given_name'],
        'last_name'     => $gpUserProfile['family_name'],
        'email'         => $gpUserProfile['email'],
        'gender'        => $gpUserProfile['gender'],
        'locale'        => $gpUserProfile['locale'],
        'picture'       => $gpUserProfile['picture'],
        'link'          => $gpUserProfile['link']
    );
    $userData = $user->checkUser($gpUserData);
	
	//Storing user data into session
	$_SESSION['userData'] = $userData;
	
	//Render facebook profile data
    if(!empty($userData)){
        $_SESSION["email"] = $userData['email']; 
        $_SESSION["login"]="1";
        header("location:../index");
        
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
} else {
    $authUrl = $gClient->createAuthUrl();
}
?>   

<script>
	  window.fbAsyncInit = function() {
		FB.init({
		  appId      : '440990667175749',
		  cookie     : true,
		  xfbml      : true,
		  version    : 'v3.1'
		});
		FB.AppEvents.logPageView();   
	  };

	  (function(d, s, id){
		 var js, fjs = d.getElementsByTagName(s)[0];
		 if (d.getElementById(id)) {return;}
		 js = d.createElement(s); js.id = id;
		 js.src = "https://connect.facebook.net/en_US/sdk.js";
		 fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	   
	   function fbLogin(){
			FB.login(function(response){
				if(response.authResponse){
					fbAfterLogin();
				}
			}, {scope: 'public_profile,email'});
	   }
	   
	   function fbAfterLogin(){
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {   // Lo
				FB.api('/me', {fields: 'name, email'}, function(response) {
				  jQuery.ajax({
					url:'f-login/check_login.php',
					type:'post',
					data:'name='+response.name+'&id='+response.id+'&email='+response.email,
					success:function(result){
					    
						window.location.href='index.php';
					}
				  });
				});
			}
		});
	   }
</script>



   
    <div id="tt-pageContent" class="show_unavailable_variants">
   <div class="container-indent">
      <div class="container">
         <h1 class="tt-title-subpages noborder">ALREADY REGISTERED?</h1>
         <div class="tt-login-form">
            <div class="row">
               <div class="col-xs-12 col-md-6">
                  <div class="tt-item">
                     <h2 class="tt-title">NEW CUSTOMER</h2>
                     <p>By creating an account with our store, you will be able to move through the checkout process faster, store multiple shipping addresses, view and track your orders in your account and more.</p>
                     <div class="form-group">
                        <a href="signup" class="btn btn-top btn-border">CREATE AN ACCOUNT</a>
                     </div>
                  </div>
               </div>
               <div class="col-xs-12 col-md-6">
                  <div class="tt-item">
                     <div id="login">
                         
                      <div class="social-login">
                        <a href="<?= filter_var($authUrl, FILTER_SANITIZE_URL); ?>" class="loginBtn loginBtn--google" style="color: #FFF;">
                          Login with Google
                        </a>
                        <a href="javascript:void(0)" scope="public_profile,email" onclick="fbLogin()" class="loginBtn loginBtn--facebook" style="color: #FFF;">
                          Login with Facebook
                        </a>
                      </div>

                        <h2 class="tt-title">LOGIN</h2>
                        If you have an account with us, please log in.
                        
                        <?php include("notifications.php"); ?>
                        
                        <div class="form-default form-top">
                           <form method="post" action="logincheck" id="customer_login" accept-charset="UTF-8">
                              <div class="tt-base-color"> </div>
                              <div class="form-group">
                                 <label for="loginInputName">E-MAIL <sup>*</sup></label>
                                 <div class="tt-required">* Required Fields</div>
                                 <input type="email" name="email" class="form-control" required id="loginInputName" placeholder="Enter E-mail">
                              </div>
                              <div class="form-group">
                                 <label for="loginInputEmail">PASSWORD <sup>*</sup></label>
                                 <input type="password" value="" name="password"  required class="form-control" id="loginInputEmail" placeholder="Enter Password">
                              </div>
                              <div class="row">
                                 <div class="col-auto mr-auto">
                                    <div class="form-group">
                                       <button class="btn btn-border" type="submit">LOGIN</button>
                                    </div>
                                 </div>
                                 <div class="col-auto align-self-end">
                                    <div class="form-group">
                                       <ul class="additional-links">
                                          <li><a href="#" onclick="showRecoverPasswordForm();return false;">Lost your password?</a></li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                     <div id="recover-password" style="display:none;" class="wrap">
                        <h2 class="tt-title">RESET PASSWORD</h2>
                        We will send you an email to reset your password.
                        <div class="form-default form-top">
                           <form method="post" action="resetpassword" accept-charset="UTF-8">
                              <div class="tt-base-color"> </div>
                              <div class="form-group">
                                 <label for="loginInputName">E-MAIL <sup>*</sup></label>
                                 <div class="tt-required">* Required Fields</div>
                                 <input value="" name="email" class="form-control" id="loginInputName" placeholder="Enter E-mail" autocorrect="off" autocapitalize="off">
                              </div>
                              <div class="row">
                                 <div class="col-auto">
                                    <div class="form-group">
                                       <button class="btn btn-border" type="submit">SUBMIT</button>
                                    </div>
                                 </div>
                                 <div class="col-auto align-self-center">
                                    <div class="form-group">
                                       <ul class="additional-links">
                                          <li>&nbsp;or <a href="#" onclick="hideRecoverPasswordForm();return false;">Cancel</a></li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <script type="text/javascript">
      function showRecoverPasswordForm() {
        document.getElementById('recover-password').style.display = 'block';
        document.getElementById('login').style.display='none';
      }
      
      function hideRecoverPasswordForm() {
        document.getElementById('recover-password').style.display = 'none';
        document.getElementById('login').style.display = 'block';
      }
      
      if (window.location.hash == '#recover') { showRecoverPasswordForm() }
   </script>
</div>
   
   
   
     <?php include("footer.php"); ?>
   
 <?php include("foot.php"); ?>
   
   
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
   
</body>
</html>