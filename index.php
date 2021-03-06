<?php 
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
    $json_playlist = file_get_contents("./up/playlist.gr3");
    $jsonArray = json_decode($json_playlist, true);
    $total_time = 0;
    $songs = 0;
    foreach($jsonArray as $track){
        $songs += 1;
        $duration = explode(":", $track['duration']);
        $min = isset($duration[0]) ? $duration[0] : 0;
        $sec = isset($duration[1]) ? $duration[1] : 0;
        $total_time += ($min * 60) + $sec; 
    }
    if(!include("./up/info.php")){
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
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>GR3Music Player</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<!--     Fonts and icons     -->
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

	<!-- CSS Files -->
    <link href="./@/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./@/assets/css/material-kit.css?v=1.2.1" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.5.10/plyr.css" />
    
    <link href="./@/assets/css/main.css" rel="stylesheet"/>
</head>

<body class="about-us">
	<nav class="navbar navbar-transparent navbar-absolute">
    	<div class="container">
        	<div class="navbar-header">
        		<a class="navbar-brand" href="https://domenicogreco.com/" target="_blank">GR3Music Player</a>
        	</div>
    	</div>
    </nav>

	<div class="page-header header-filter header-small" data-parallax="true" style="background-image: url('<?php echo file_get_contents("https://gr3studios.com/API/images/random_pixabay/index.php"); ?>');" title="images by pixabay">
		<div class="container">
    		<div class="row">
        		<div class="col-md-8 col-md-offset-2">
                    <h1 class="title"><i class="fas fa-music"></i> <?php echo urldecode($pl_name); ?> <i class="fas fa-music"></i></h1>
                        <h4><?php echo urldecode($pl_author); ?> - <?=$songs?> songs<br>Duration: <?=gmdate("H:i:s",$total_time)?></h4>
                </div>
            </div>
        </div>
	</div>

	<div class="main main-raised">
		<div class="container">
            <div class="about-description text-center">
                <div class="row">
                    <?php
                        if(filesize($json_playlist) < 16 && empty(trim(($json_playlist))) ){
                            echo '<center><h2>This playlist is empty! <i class="fas fa-helicopter"></i></h2></center><br><br><br><br>';
                            $json_playlist = "[]";
                        }else{
                            if(is_null(json_decode($json_playlist, true)[0])){
                                echo '<center><h2>This playlist is empty! <i class="fas fa-helicopter"></i></h2></center><br><br><br><br>';
                                $json_playlist = "[]";
                            }else{
                                echo '<div class="col-md-10 col-md-offset-1">
                                        <div class="table-responsive">
                                            <ul class="list-group" id="plList">
                                            </ul>
                                            <br><br>
                                        </div>
                                    </div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <nav class="pull-left">
				<ul>
				    <li>
						<a target="_blank" href="up/" style="color: #DAA520;">
							<i class="fas fa-crown"></i> MANAGE
						</a>
					</li>
					<li>
						<a target="_blank" href="https://github.com/gr3studios/php-Music-Player">
							<i class="fab fa-github"></i> VIEW ON GITHUB
						</a>
					</li>
					<li>
						<a target="_blank" href="https://domenicogreco.com">
						   <i class="fa fa-link"></i> GR3Studios.com
						</a>
					</li>
				</ul>
            </nav>
            <div class="copyright pull-right">
                &copy; <script>document.write(new Date().getFullYear())</script>, made with <i class="fa fa-heart heart"></i> by <a href="https://domenicogreco.com" target="_blank">Domenico Greco</a>.
            </div>
        </div>
    </footer>
    <div id="space_div"></div>
    <div class="main main-raised fixed_player" id="player_div" style="margin-bottom:10px;">
		<div class="container">
            <div class="about-description text-center" style="padding: 15px 0px 0px 0px;">
                <div class="row">
    				<div class="col-md-12">
           
                        <div class="row">
                            <div class="col-sm-12" title="Titolo del brano corrente">
                                &nbsp;&nbsp;&nbsp;&nbsp;<a id="npTitle" style="color:white; font-weight: bold; cursor: default;"></a>
                            </div>
                        </div>
                        <div style="float:center; display:block; width:100%; color:white;" align="center">
                           <audio id="musician" preload controls>Your browser not support HTML5 audio! 😢</audio>
                        </div>
                        <div style="float:left; display:block; width:15%; color:white;" align="left">
                            &nbsp;&nbsp;&nbsp;&nbsp;<a id="btnPrev" style="cursor: pointer;"><i class="fas fa-arrow-left"></i></a>
                        </div>
                        <div style="float:left; display:block; width:70%; color:white;" title="Click on Play" align="center">
                            <a id="npAction" style="color:white;cursor: default;" title="Click on Play">NO REPRODUCTION</a>
                        </div>
                        <div style="float:left; display:block; width:15%; color:white;" align="right">
                            <a id="btnNext" style="cursor: pointer;"><i class="fas fa-arrow-right"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>
                        <div style="clear:both;"></div>

    				</div>
    			</div>
            </div>
        </div>
    </div>

</body>
    <script>
        var player = document.getElementById('player_div').offsetHeight;
        document.getElementById('space_div').style.paddingBottom=(player)+"px";
    </script>
	<!--   Core JS Files   -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<script src="./@/assets/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="./@/assets/js/material.min.js"></script>
	<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select   -->
	<script src="./@/assets/js/bootstrap-selectpicker.js" type="text/javascript"></script>
	<!--	Plugin for Tags, full documentation here: http://xoxco.com/projects/code/tagsinput/   -->
	<script src="./@/assets/js/bootstrap-tagsinput.js"></script>
	<!--    Control Center for Material Kit: activating the ripples, parallax effects, etc    -->
	<script src="./@/assets/js/material-kit.js?v=1.2.1" type="text/javascript"></script>
	<!-- html5media -->
	<script src='https://cdnjs.cloudflare.com/ajax/libs/html5media/1.1.8/html5media.min.js'></script>
    <script src="https://cdn.plyr.io/3.5.10/plyr.polyfilled.js"></script>
	<!-- Main Script -->
        <script>

            jQuery(function ($) {
                'use strict'
                var supportsAudio = !!document.createElement('audio').canPlayType;
                if (supportsAudio) {
                    // initialize plyr
                    var player = new Plyr('#musician', {
                        controls: [
                            'play',
                            'progress',
                            'current-time',
                            'duration',
                            'mute',
                            'volume'
                        ]
                    });
                    // pls e controls
                    var index = 0,
                        playing = false,
                        mediaPath = './media/',
                        extension = '',
                        tracks = <?=$json_playlist?>,
                        buildPlaylist = $.each(tracks, function(key, value) {
                            var trackNumber = value.track,
                                trackName = value.name,
                                trackDuration = value.duration,
                                authors = value.artist,
                                mime = value.mime,
                                extension = "."+value.ext;
                            if (trackNumber.toString().length === 1) {
                                trackNumber = '0' + trackNumber;
                            }
                            $('#plList').append('<li id="li_'+trackNumber+'" title="Track id ' + trackNumber + '" class="list-group-item track" id="cpt" align="left" style="border-right: 0 none;border-left: 0 none;"> \
                                <h4 class="list-group-item-heading" class="plTitle"><span id="title_'+trackNumber+'">' + trackName + '</span> <span align="right" class="plLength">' + trackDuration + '</span></h4> \
                                <p class="list-group-item-text"><a id="author">by <b>' + authors + '</b></a></p> \
                            </li>');
                            var track_width = $('#li_'+trackNumber).width()-50;
                            var title_width = $('#title_'+trackNumber).width();
                            if(title_width > track_width){
                                var length = 30;
                                var trackName = trackName.length > length ? 
                                    trackName.substring(0, length - 3) + "..." : 
                                    trackName;
                                $('#title_'+trackNumber).text(trackName);
                            }
                        }),
                        trackCount = tracks.length,
                        npAction = $('#npAction'),
                        npTitle = $('#npTitle'),
                        audio = $('#musician').on('play', function () {
                            playing = true;
                            npAction.text('PLAYING');
                        }).on('pause', function () {
                            playing = false;
                            npAction.text('PAUSE');
                        }).on('ended', function () {
                            npAction.text('PAUSE');
                            if ((index + 1) < trackCount) {
                                index++;
                                loadTrack(index);
                                audio.play();
                            } else {
                                audio.pause();
                                index = 0;
                                loadTrack(index);
                            }
                        }).get(0),
                        btnPrev = $('#btnPrev').on('click', function () {
                            if ((index - 1) > -1) {
                                index--;
                                loadTrack(index);
                                if (playing) {
                                    audio.play();
                                }
                            } else {
                                audio.pause();
                                index = 0;
                                loadTrack(index);
                            }
                        }),
                        btnNext = $('#btnNext').on('click', function () {
                            if ((index + 1) < trackCount) {
                                index++;
                                loadTrack(index);
                                if (playing) {
                                    audio.play();
                                }
                            } else {
                                audio.pause();
                                index = 0;
                                loadTrack(index);
                            }
                        }),
                        li = $('#plList li').on('click', function () {
                            var id = parseInt($(this).index());
                            if (id !== index) {
                                playTrack(id);
                            }
                        }),
                        loadTrack = function (id) {
                            $('.active').removeClass('active');
                            $('#plList li:eq(' + id + ')').addClass('active');
                            npTitle.text(tracks[id].name);
                            index = id;
                            audio.src = mediaPath + tracks[id].file + "." + tracks[id].ext;
                            audio.type = tracks[id].mime;
                            updateDownload(id, audio.src);
                            if ('mediaSession' in navigator) { // control music by chrome notification (on mobile)
                              navigator.mediaSession.metadata = new MediaMetadata({
                                title: tracks[id].name,
                                artist: tracks[id].artist
                              });
                              navigator.mediaSession.setActionHandler('play', function() { audio.play(); });
                              navigator.mediaSession.setActionHandler('pause', function() { audio.pause(); });
                              navigator.mediaSession.setActionHandler('previoustrack', function() {
                                if ((index - 1) > -1) {
                                    index--;
                                    loadTrack(index);
                                    if (playing) {
                                        audio.play();
                                    }
                                } else {
                                    audio.pause();
                                    index = 0;
                                    loadTrack(index);
                                }
                              });
                              navigator.mediaSession.setActionHandler('nexttrack', function() {
                                if ((index + 1) < trackCount) {
                                    index++;
                                    loadTrack(index);
                                    if (playing) {
                                        audio.play();
                                    }
                                } else {
                                    audio.pause();
                                    index = 0;
                                    loadTrack(index);
                                }
                              });
                            }
                        },
                        onNotificationUpdate = function () {
                            player.on('loadedmetadata', function () {
                                $('a[data-plyr="download"]').attr('href', source);
                            });
                        },
                        updateDownload = function (id, source) {
                            player.on('loadedmetadata', function () {
                                $('a[data-plyr="download"]').attr('href', source);
                            });
                        },
                        playTrack = function (id) {
                            loadTrack(id);
                            audio.play();
                        };
                        
                    loadTrack(index);
                } else {
                    // no audio support
                    $('.column').addClass('hidden');
                    var noSupport = $('#musician').text();
                    $('.container').append('<p class="no-support">' + noSupport + '</p>');
                }
            });
        </script>
</html>
