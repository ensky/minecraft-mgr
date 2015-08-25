<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Minecraft Server status</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
</head>

<body>
	<div class="container">
		<h1>Minecraft Server status</h1>
		<p>ip: ensky.tw</p>
		<p>啟動狀態: <span id="status"></span></p>
		<button class="toggle hidden-xs btn">啟動</button>
		<button class="toggle visible-xs-block btn btn-block">啟動</button>
		<a target="_blank" href="map" class="hidden-xs btn btn-default">MAP!</a>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Action</th>
					<th>Name</th>
					<th>IP</th>
					<th>Status</th>
					<th>Map</th>
				</tr>
			</thead>
			<tbody id="tbody"></tbody>
		</table>
		<br><br>
		<div class="row">
			<img class="col-md-8 hidden-xs img-responsive" src="https://images-eds-ssl.xboxlive.com/image?url=8Oaj9Ryq1G1_p3lLnXlsaZgGzAie6Mnu24_PawYuDYIoH77pJ.X5Z.MqQPibUVTcRY.yavzo7nYP0X88I63UeJxs_ICOvM1iX20FQwMAmM_NGf2PodioxJlXeVtHatYGRJBIPfMhy_BiqqPoi7JdnkTPLMVQUKke7MXg5lM5XcXKfgntlW9iQcqf8zpk76BldA8GgLiMLoy78RsNUyw20Pd_86kNY7GdVNagox9w1XM-&format=jpg">
			<img class="col-xs-12 visible-xs-block img-responsive" src="http://gamunation.com/wp-content/uploads/2014/12/minecraftXCMqB.png">
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script>
	function getStringFormatPlaceHolderRegEx(placeHolderIndex) {
		return new RegExp('({)?\\{' + placeHolderIndex + '\\}(?!})', 'gm');
	}
	function cleanStringFormatResult(txt) {
		if (txt == null) return "";
		return txt.replace(getStringFormatPlaceHolderRegEx("\\d+"), "");
	}

	String.prototype.format = function () {
		var txt = this.toString();
		for (var i = 0; i < arguments.length; i++) {
			var exp = getStringFormatPlaceHolderRegEx(i);
			txt = txt.replace(exp, (arguments[i] == null ? "" : arguments[i]));
		}
		return cleanStringFormatResult(txt);
	};

	var gLastStat = {};
	var gLastClass = {};
	var stat = {
		start: function (name) {
			setStatus(name, '已啟動');
			setBtn(name, 'danger', '停止', false, 'stop');
		},
		stop: function (name) {
			setStatus(name, '已停止');
			setBtn(name, 'success', '啟動', false, 'start');
		},
		starting: function (name) {
			setStatus(name, '啟動中');
			setBtn(name, '', '啟動中', true);
			get(name, true);
		},
		stopping: function (name) {
			setStatus(name, '停止中');
			setBtn(name, '', '停止中', true);
			get(name, true);
		}
	};

	var setStatus = function (name, text) {
		$('#{0} .status'.format(name)).text(text);
	};

	var method = function (name, api) {
		$.get('http://ensky.tw:3000/api/{0}/{1}'.format(api, name), function (json) {
            gLastStat[name] = json.stat;
            stat[json.stat](name);
        });
	};

	var setBtn = function (name, cls, text, disabled, api) {
		var $btns = $('#{0} .toggle'.format(name));
		gLastClass[name] = gLastClass[name] || '';

		$btns.text(text)
			.removeClass(gLastClass[name]);
		if (cls.length !== 0) {
			cls = 'btn-' + cls;
			$btns.addClass(cls);
			gLastClass[name] = cls;
		}
		if (disabled) {
			$btns.attr('disabled', 'disabled');
		} else {
			$btns.removeAttr('disabled');
		}
		if (api) {
			$btns.unbind().bind('click', method.bind(window, name, api));
		}
	};

	var get = function (name, polling) {
		gLastStat[name] = gLastStat[name] || 'stop';
		var lastStat = gLastStat[name];
		$.get('http://ensky.tw:3000/api/status/' + name, function (json) {
			if (!polling || lastStat !== json.stat) {
				stat[json.stat](name);
			} else if (lastStat === json.stat) {
				setTimeout(get.bind(window, name, polling), 500);
			}
			lastStat = json.stat;
		});
	};

	var list = function () {
		var tbody = $('#tbody');
		$.get('http://ensky.tw:3000/api/list', function (json) {
			var servers = json.servers;
			servers.forEach(function (server) {
				tbody.append('<tr id="{0}"><td><button class="toggle hidden-xs btn">啟動</button></td><td>{0}</td><td>ensky.tw:{1}</td><td class="status">{2}</td><td>{3}</td></tr>'.format(server.name,
						server.conf.port || 25565,
						server.stat,
						server.conf.map ? '<a href="{0}" class="btn btn-default">MAP!</a>'.format(server.conf.map) : ""));
				get(server.name, false);
			});
		});
	}

	list();
	</script>
</body>

</html>
