<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

class ACCESS{
    public $user_credentials_file = "../up/udata.php";
    function __construct(){
        global $up_2;
        session_start();
        $up_2 = 0;
    }
    public function logout(){
        global $_SESSION;
        $_SESSION['logged'] = false;
        session_destroy();
        return header("Location: ?logged=[false]");
    }
    public function is_logged(){
        global $_SESSION;
        if(isset($_SESSION['logged']) && $_SESSION['logged']){
            return true;
        }else{
            return false;
            die();
        }
    }
    public function signup($username, $password){
        global $is_new;
        if(include($this->user_credentials_file)){
            die("NOT PERMITTED!");
        }else{
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $udata = fopen($this->user_credentials_file, "w") or die("Unable to write file!<br>Check permission");
            fwrite($udata, "<?php \$real_username='".htmlspecialchars($username)."'; \$real_password='".$hashed_password."'; ?>");
            fclose($udata);
            $is_new = 1;   
        }
    }
    public function login($username, $password){
        global $_SESSION;
        include($this->user_credentials_file);
        if(htmlspecialchars($username) == $real_username && password_verify($password, $real_password)){
            $_SESSION['logged'] = true;
            return true;
        }else{
            return false;
        }
    }
}
$access = new ACCESS();

class getAudio{
    public $getid3_folder = "../@/php-getid3/";
    public function info($file){
        if(!include($this->getid3_folder.'getid3/getid3.php')){
            return die("Error Basic library not included [lib: getid3]");
        }
        // Initialize getID3 engine
        $getID3 = new getID3;
        // Analyze file
        $FileInfo = $getID3->analyze($file);
        return array(
            "title" => $FileInfo['tags']['id3v2']['title'][0],
            "duration" => $FileInfo['playtime_string'],
            "ext" => $FileInfo['fileformat'],
            "file_name" => $FileInfo['filename'],
            "file_path" => $FileInfo['filepath'],
            "file_size" => $FileInfo['filesize'],
            "mime" => $FileInfo['mime_type'],
            "artist" => $getID3->analyze($file)['id3v1']['artist']
            );

    }
}

class GR3Manager{
    public $media_dir = "../media/";
    public $tmp_folder = "../up/tmp/";
    public $playlist_file = "../up/playlist.gr3";
    public $allow_duplicate = true;
    public $up_exts = array(".mp3", ".ogg", ".m4a", ".webm");
    public $max_audio_size = 39097152;

    public function playlistToArray($playlist){
        if(empty($playlist)){
            $playlist = $this->playlist_file;
        }
        $array = json_decode(file_get_contents($playlist), true);
        if(is_null($array) or empty($array)){
            return array();
        }else{
            return $array;
        }
    }
    public function arrayToPlaylist($array){
        return json_encode(array_values($array));
    }
    public function rewritePlaylist($data){
        $f=fopen($this->playlist_file,'w');
        fwrite($f,$data);
        fclose($f);
        return true;
    }
    public function add_in_playlist($track_num, $track_name, $track_duration, $file_name, $ext, $mime, $artist){
        $asx = $this->playlistToArray("");
        array_unshift($asx , array("track"=>$track_num,"name"=>$track_name,"duration"=>$track_duration,"file"=>$file_name,"ext"=>$ext,"mime"=>$mime,"artist"=>$artist));
        $this->rewritePlaylist($this->arrayToPlaylist($asx));
        return true;
    }
    public function clearTMP(){
        $dirPath = ($this->tmp_folder); 
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        return true;
    }
    public function genName($ext = ""){ // numTracks
        $time = time();
        return array("oci-".$time.".".$ext, "oci-".$time);
    }
    public function totalTracks(){ 
        $files = glob($this->media_dir . "*");
        if ($files){
            return $this->playlistToArray("")[0]['track'];
        }else{
            return 0;
        }
    }
    public function uploadTrack_1(){
        global $_FILES, $up_2; 
        $this->clearTMP(); // clear tmp folder
        $file_name = $_FILES['audio']['name'];
        $file_size = $_FILES['audio']['size'];
        $file_tmp = $_FILES['audio']['tmp_name'];
        $file_ext = strtolower(end(explode('.',$file_name)));
        if(in_array(".".$file_ext,$this->up_exts)=== false){
            return array(false, "Extension not allowed.");
            exit();
        }
        if($file_size > $this->max_audio_size) {
            return array(false, "File size error");
            exit();
        }
        $new_name = $this->genName($file_ext);
        move_uploaded_file($file_tmp,$this->tmp_folder.$new_name[0]);
        $up_2 = true;
        $getAudio = new getAudio;
        $audio_info = $getAudio->info($this->tmp_folder.$new_name[0]);
        $track_name = $audio_info['title'];
        $track_duration = $audio_info['duration'];
        if(empty($track_name)){ $track_name = basename($file_name, ".".$file_ext); }
        if($track_name == "mp4"){ $track_name = "m4a"; }
        $track_mime = $audio_info['mime'];
        $track_artist =$audio_info['artist'];
        return array(true, array($this->tmp_folder.$new_name[0], $track_name, $file_ext, htmlspecialchars($track_duration), base64_encode($new_name[1]), $track_mime, $track_artist));
    }
    public function uploadTrack_2(){
        global $_POST, $up_2;
        $up_2 = false;

        $track_num = $this->totalTracks() + 1;
        if(empty($_POST['name'])){
            $track_name = "Track ".$track_num;
        }else{
            $track_name = htmlspecialchars($_POST['name']);
        }
        $track_artist = htmlspecialchars($_POST['track_artist']);
        if(is_null($track_artist) or empty($track_artist) or $track_artist == "" or $track_artist == "1" or $track_artist == 1 or $track_artist == "true"){
            $track_artist = "Unknown Artist";
        }
        $track_duration = htmlspecialchars_decode($_POST['duration']);
        $track_file = base64_decode($_POST['track_id_info']);
        $track_mime = base64_decode($_POST['track_mime']);
        // update playlist
        $this->add_in_playlist($track_num, $track_name, $track_duration, $track_file, htmlspecialchars($_POST['ext']), $track_mime, $track_artist);
        // moving file from tmp folder to media folder
        $name = scandir($this->tmp_folder, 1)[0];
        rename($this->tmp_folder.$name, $this->media_dir.$name);
        
        $this->clearTMP(); // clear tmp folder
        return array(true, $track_name);
    }
    public function removeTrack($track_id){
        $tracks = $this->playlistToArray("");
        $index = 0;
        foreach ($tracks as $track) {
            if($track['track'] == $track_id){
                $name_saved = $track['name'];
                $file_saved = $track['file'];
                unlink($this->media_dir.$file_saved.".".$track['ext']);
                unset($tracks[$index]);
                break;
            }
            $index++;
        }
        $this->rewritePlaylist($this->arrayToPlaylist($tracks));
        return array(true, $name_saved);
    }
    public function check_duplicate($file){
        $tracks = $this->playlistToArray("");
        if(!is_null($tracks) or $tracks != ""){
            foreach($tracks as $track){
                if(md5_file($file) == md5_file($this->media_dir.$track['file'].".".$track['ext'])){
                    return array(true, $track['name']);
                }
            }
        }
        return array(false, false);
    }
}
$gr3manager = new GR3Manager();
?>
