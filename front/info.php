<?php

function my_socket_connect (&$sock, $ip, $port) {
	global $timeout;
	socket_set_nonblock($sock);
	$time = microtime(True);
	while (!@socket_connect($sock, $ip, $port))
	{
		$err = socket_last_error($sock);
		socket_clear_error($sock);
		if ( $err == 36 )
		{
			// still connecting
			if ((microtime(True) - $time) >= $timeout)
			{
				socket_close($sock);
				// throw new Exception('timeout');
				return False;
			}
			// 0.1 seconds
			usleep(10000);
		} else if ($err == 56) {
			// already connected.
			break;
		} else {
			// throw new Exception(socket_strerror($err) . "\n");
			return False;
		}
	}
	socket_set_block($sock);
	return True;
}

$ip = 'ensky.tw';
$port = '25565';
$timeout = 1;

$b = pack('c', 0xfe);
$t = microtime(True);
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$timeout = array('sec'=>1,'usec'=>0);
socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout);
socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, $timeout);
if ( my_socket_connect($sock, $ip, $port) ) {
	if ( !socket_write($sock, $b) ) {
		$num = -1;
	} else {
		$str = socket_read($sock, 2048);
		$num = intval(substr($str, 42, 1));
		if (167 !== ord(substr($str, 44, 1))) { // 十位數
			$num = $num * 10 + intval(substr($str, 44, 1));
		}
		$speed = new stdclass();
		$ping = (microtime(True) - $t) * 1000;
		$speed->ping = round($ping);
		if ( $speed->ping < 100 ) {
			$speed->label = 'success';
			$speed->chinese = '良好';
		} else if ($speed->ping < 200) {
			$speed->label = 'warning';
			$speed->chinese = '普通';
		} else {
			$speed->label = 'important';
			$speed->chinese = '龜速';
		}
	}
} else {
	$num = -1;
}
socket_close($sock);



?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="refresh" content="60; url=index.php" />
	<title>Minecraft Server status</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
</head>

<body>
	<div class="container-fluid">
		<div class="content">
			<h1>Minecraft Server status</h1>
			<p>ip:port <?php echo $ip.':'.$port; ?></p>
<?php /* 
			<p>
				Server狀態: 
					<?php if ( $num == -1 ) { ?><span class="label">掛了</span><?php } 
						  else { ?><span class="label <?php echo $speed->label; ?>">OK, 速度<?php echo $speed->chinese; ?>, ping: <?php echo $speed->ping; ?>ms</span><?php } ?>
			</p>
			<?php if ($num != -1) : ?>
			<p>線上人數: <?php echo $num; ?></p>
			<?php endif; ?>
*/ ?>
			<p>啟動狀態: <span id="status"></span></p>
			<button id="toggle" class="btn btn-xlarge">啟動</button>
		</div>
<?php /* 		<p><br><br><img class="img-responsive" src='https://images-eds-ssl.xboxlive.com/image?url=8Oaj9Ryq1G1_p3lLnXlsaZgGzAie6Mnu24_PawYuDYIoH77pJ.X5Z.MqQPibUVTcRY.yavzo7nYP0X88I63UeJxs_ICOvM1iX20FQwMAmM_NGf2PodioxJlXeVtHatYGRJBIPfMhy_BiqqPoi7JdnkTPLMVQUKke7MXg5lM5XcXKfgntlW9iQcqf8zpk76BldA8GgLiMLoy78RsNUyw20Pd_86kNY7GdVNagox9w1XM-&format=jpg'></p> */ ?>
	</div>
	<script>
	var lastStat = null;
	var stat = {
		start: function () {
			setStatus('已啟動');
			setBtn('danger', '停止', false, 'stop');
		},
		stop: function () {
			setStatus('已停止');
			setBtn('success', '啟動', false, 'start');
		},
		starting: function () {
			setStatus('啟動中');
			setBtn('', '啟動中', true);
			get(true);
		},
		stopping: function () {
			setStatus('停止中');
			setBtn('', '停止中', true);
			get(true);
		}
	};
	var setStatus = function (text) {
		$('#status').text(text);
	};
	var method = function (method) {
		$.get('http://ensky.tw:3000/api/' + method, function (json) {
            json = JSON.parse(json);
            lastStat = json.stat;
            stat[json.stat]();
        });
	};
	var setBtn = function (cls, text, disabled, api) {
		$('#toggle').attr('class', 'btn');
		if (cls.length !== 0) {
			$('#toggle').addClass('btn-' + cls);
		}
		$('#toggle').text(text);
		if (disabled) {
			$('#toggle').attr('disabled', 'true');
		} else {
			$('#toggle').removeAttr('disabled');
		}
		if (api) {
			$('#toggle').unbind().bind('click', method.bind(window, api));
		}
	};
	var get = function (polling) {
		$.get('http://ensky.tw:3000/api/status', function (json) {
			json = JSON.parse(json);
			if (!polling || lastStat !== json.stat) {
				stat[json.stat]();
			} else if (lastStat === json.stat) {
				setTimeout(get.bind(window, true), 500);
			}
			lastStat = json.stat;
		});
	};
	get(false);
	</script>
</body>

</html>
