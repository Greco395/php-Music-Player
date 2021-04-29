<?php
    require_once("../@/dj.php");
?>
<?php 
if(isset($_POST['case']) && $_POST['case'] == "signup"){
    $access->signup($_POST['username'], $_POST['password']);
}
if(!include($access->user_credentials_file)){ ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <title>SET LOGIN DATAS - GR3Music</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- Google Fonts -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
            <!-- Bootstrap core CSS -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
            <!-- Material Design Bootstrap -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.14.1/css/mdb.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="jumbotron text-center">
                <h1 style="color:red;">SET LOGIN DATAS</h1>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <form action="" method="POST">
                            <input type="hidden" name="case" value="signup" />
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            </div>
                            <small class="form-text text-muted">If you forgot them, delete the file: ~/up/udata.php</small>
                            <br>
                            <button type="submit" class="btn btn-block btn-danger">SIGNUP</button>
                        </form>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php    
        die();
    }
?>
<?php 
    if(isset($_GET['case']) && $_GET['case'] == "logout"){
        $access->logout();
    }
    if(isset($_POST['case']) && $_POST['case'] == "login"){
        if($access->login($_POST['username'], $_POST['password'])){
            header("Location: ?logged=[true]");
        }else{
            $alert = "Incorrect credentials";
        }
    }
    if(!$access->is_logged()){ ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <title>LOGIN - GR3Music</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width,m initial-scale=1">
            <!-- Google Fonts -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
            <!-- Bootstrap core CSS -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
            <!-- Material Design Bootstrap -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.14.1/css/mdb.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="jumbotron text-center">
                <h1>ADMIN PANEL LOGIN</h1>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <?php if(isset($is_new)){?>
                            <div class="alert alert-success" role="alert">
                                <center>
                                    <b>
                                        DATAS SAVED!<br>Please Login
                                    </b>
                                </center>
                            </div>
                        <?php } ?>
                        <?php if(isset($alert)){?>
                            <div class="alert alert-danger" role="alert">
                                <center>
                                    <b>
                                        <?=$alert?>
                                    </b>
                                </center>
                            </div>
                        <?php } ?>
                        <form action="" method="POST">
                            <input type="hidden" name="case" value="login" />
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            </div>
                            <small class="form-text text-muted">If you have forgotten them, delete the file: ~/up/udata.php</small>
                            <br>
                            <button type="submit" class="btn btn-block btn-primary">LOGIN</button>
                        </form>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php    
        die();
    }
?>
<?php
    if(isset($_FILES['audio'])){
        $status = $gr3manager->uploadTrack_1($_FILES);
        if(!$status[0]){
            $msg = $status[1];
        }else{ 
            $file_path_tmp = $status[1][0];
            $file_name_tmp = $status[1][1];
            $file_ext_tmp = $status[1][2];
            $file_duration_tmp = $status[1][3];
            $track_id_info_tmp = $status[1][4];
            $track_mime = $status[1][5];
            $track_artist = $status[1][6];
        }
    } 
    
    if(isset($_POST['case']) && $_POST['case'] == "up_2") {
        $status = $gr3manager->uploadTrack_2();
        $msg = "Track uploaded!<br>Track: ".$status[1];
    }
    
    if(isset($_POST['case']) && $_POST['case'] == "rm_audio"){
        $status = $gr3manager->removeTrack($_POST['trk_to_rm']);
        $msg = "Track Removed<br>Track: ".$status[1];
    }
    if(isset($_POST['case']) && $_POST['case'] == "clear_tmp"){
        $gr3manager->clearTMP();
        header("Location: ?home");
    }
    if(isset($_POST) && $_POST['case'] == "save_info"){
        $f=fopen("info.php",'w');
        fwrite($f,"<?php \$pl_name='".urlencode($_POST['name'])."'; \$pl_author='".urlencode($_POST['author'])."'; ?>");
        fclose($f);
        $msg = "Playlist Info Saved!";
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ADMIN PANEL</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Google Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
        <!-- Bootstrap core CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <!-- Material Design Bootstrap -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.14.1/css/mdb.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
    <h6 align="right"><a class="btn btn-sm btn-danger" href="?case=logout">LOGOUT</a></h6>
    <div class="jumbotron text-center">
        <h1>MUSIC ADMIN PANEL</h1>
    </div>
    <?php if(isset($msg)){?>
    <div class="alert alert-warning" role="alert">
        <center>
            <b>
                <?=$msg?>
            </b>
        </center>
    </div>
    <?php } 
    if(!$up_2){
        if(!include("info.php")){
            $pl_name = "Generic Playlist";
            $pl_author = "Unkown Owner";
        }else{
            if(!isset($pl_name) or empty($pl_name)){
                $pl_name = "Generic Playlist";
            }
            if(!isset($pl_author) or empty($pl_author)){
                $pl_author = "Unkown Owner";
            }
        }
    ?>
    <div class="container">
        <center><h2>PLAYLIST INFO</h2></center><br>
      <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <form action = "" method = "POST">
                <input type="hidden" name="case" value="save_info">
                <div class="form-group">
                    <label>Playlist Name</label>
                    <input type="text" name="name" value="<?php echo urldecode($pl_name); ?>" class="form-control" placeholder="Enter a name for your playlist">
                </div>
                <div class="form-group">
                    <label>Playlist Author</label>
                    <input type="text" name="author" value="<?php echo urldecode($pl_author); ?>" class="form-control" placeholder="Enter your name">
                </div>
                <br><br>
                <button type="submit" class="btn btn-warning btn-block">SAVE</button>
            </form>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div><br>
    <hr>
    <?php } ?>
    <div class="container">
        <center><h2><?php if(!$up_2){ echo "UPLOAD"; }else{ echo "<font color='red'>CONFIRM MEDIA</font>"; } ?></h2></center><br>
      <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <?php if(!$up_2){ ?>
            <hr><?php echo file_get_contents("https://greco395.com/API/gr3music/tip.php"); ?><hr><br>
            <form action = "" method = "POST" enctype = "multipart/form-data">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" name = "audio" style="cursor:pointer;">
                  <label style="cursor:pointer;" class="custom-file-label" for="customFile">Choose an audio file to upload</label>
                </div><br><br>
                <button type="submit" class="btn btn-success btn-block">CONTINUE</button>
                <br><hr>
                <b>Allowed Audio Extension</b>: <?php 
                    $inx=0;
                    $cnt = count($gr3manager->up_exts);
                    foreach($gr3manager->up_exts as $extallowed){
                        echo substr($extallowed, 1);
                        if($inx != $cnt-1){ echo ", "; }else{ echo "."; }
                        $inx++;
                    }
                ?>
            </form>
            <?php }else{ ?>
            <form action="" method="POST" id="form">
                <input type="hidden" name="case" value="up_2">
                <input type="hidden" name="ext" value="<?php echo $file_ext_tmp; ?>">
                <input type="hidden" name="duration" value="<?php echo $file_duration_tmp; ?>">
                <input type="hidden" name="track_id_info" value="<?php echo $track_id_info_tmp;?>">
                <input type="hidden" name="track_mime" value="<?php echo base64_encode($track_mime); ?>">
                <?php 
                    if($gr3manager->allow_duplicate){
                        $check = $gr3manager->check_duplicate($file_path_tmp);
                        if($check[0]){?>
                            <div class="alert alert-danger" role="alert">
                              This file already exist on this playlist!<br>
                              Duplicate name: <?=$check[1]?><br><br>
                              <form actin="" method="POST">
                                  <input type="hidden" name="case" value="clear_tmp">
                                  <button type="submit" class="btn btn-warning">RETURN BACK</button>
                              </form>
                            </div>
                        <?php }
                    }
                ?>
                <div class="form-group">
                    <label>Track</label><br>
                    <audio id="myAudio" controls style="width: 100%;">
                      <source src="<?php echo $file_path_tmp; ?>" type="audio/mpeg">
                      Your browser does not support the audio element.
                    </audio>
                </div>
                <div class="form-group">
                    <label>Track Name (editable)</label>
                    <input type="text" name="name" value="<?php echo urldecode($file_name_tmp); ?>" class="form-control" placeholder="Enter a name for your track">
                </div>
                <br>
                <div class="form-group">
                    <label>Artist</label>
                    <input type="text" name="track_artist" value="<?php echo ($track_artist); ?>" class="form-control" placeholder="Enter the name of track author">
                </div>
                <br>
                <button type="submit" class="btn btn-block btn-primary">CONFIRM UPLOAD</button>
            </form>
            <form actin="" method="POST">
                <input type="hidden" name="case" value="clear_tmp">
                <button type="submit" class="btn btn-warning">NOT UPLOAD</button>
            </form>
            <?php } ?>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div>
    <?php if(!$up_2){ ?>
    <hr>
    <div class="container">
        <center><h2>REMOVE TRACK</h2></center><br>
      <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <form action = "" method = "POST">
                <input type="hidden" name="case" value="rm_audio">
                <select class="custom-select" name="trk_to_rm">
                  <option selected>Select a track</option>
                  <?php
                    $tracks = $gr3manager->playlistToArray("");
                    $index = 0;
                    foreach($tracks as $track){
                        echo "<option value=\"".$tracks[$index]['track']."\">".$tracks[$index]['name']."</option>";
                        $index++;
                    }
                    ?>
                </select>
                <br><br>
                <button type="submit" class="btn btn-danger btn-block">REMOVE</button>
            </form>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div>
    <script>
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    </script>
    <?php } ?>
    <div style="background-color:#161616;padding-top:30px;padding-bottom:10px;color:white; margin-top:50px;" align="center" class="footer text-center">
          <h1><a href="https://domenicogreco.com" target="_blank">Greco395</a></h1>
          Watch on <a href="https://github.com/Greco395/GR3Music-Player" target="_blank">github</a> or <a href="../">ear a track.</a><br><br>
        </div>
    
    </body>
</html>
