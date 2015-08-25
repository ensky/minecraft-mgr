var spawn = require('child_process').spawn,
	express = require('express'),
	YAML = require('yamljs'),
	log = console.log,
	gConfig = YAML.load('./config.yaml'),
	gServers = {},
	logBuffer = function () {
		var pub = {},
			buf = [],
			maxLen = 10;

		pub.log = function (msg) {
			buf.push(msg);
			pub.trim();
		};

		pub.trim = function () {
			while (buf.length > 60) {
				buf.shift();
			}
		};

		pub.get = function () {
			return buf;
		};

		return pub;
	}();

var MCServer = function (conf) {
	var pub = {},
		minecraft = null,
		stat = 'stop';

	pub.start = function () {
		if (minecraft !== null) {
			return ;
		}
		stat = 'starting';
		log('minecraft starting');

		minecraft = spawn('/usr/home/ensky/minecraft/'+ conf.name +'/start.sh', [], { cwd: '/usr/home/ensky/minecraft/'+ conf.name +'/' });

		minecraft.stdout.on('data', function (data) {
			var str = data.toString().replace(/\n/, '');
			if (str.match(/Done \([\d.]+s\)/)) {
				stat = 'start';
				log('minecraft started');
			}
			log(str);
			logBuffer.log(str);
		});

		minecraft.on('close', function () {
			log('minecraft stopped');
			minecraft = null;
			stat = 'stop';
		});
	};

	pub.stop = function () {
		if (minecraft === null) {
			return ;
		}
		minecraft.stdin.write("stop\n");
		stat = 'stopping';
	};

	pub.getOnlinePromise = function () {
		if (stat !== 'start') {
			return Promise.resolve([]);
		}

		return new Promise(function (resolve, reject) {
			var count = 0;
			var callback = function (data) {
				var str = data.toString().replace(/\n$/, '');
				if (str.match(/There are \d+\/\d+ players online/)) {
					count = 0;
				} else if (count === 1) {
					var msg = str.split(' ');
					msg.shift();
					msg.shift();
					msg.shift();
					resolve(msg);
				} else if (count === 2) {
					minecraft.stdout.removeListener('data', callback);
				}
				count++;
			};
			minecraft.stdout.on('data', callback);
			minecraft.stdin.write('list\n');
		});
	};

	pub.sendStat = function (res) {
		res.json({stat: stat, conf: conf});
	};

	pub.getStat = function () {
		return stat;
	};

	pub.getConf = function () {
		return conf;
	};

	return pub;
};

(function () {
	var serverConf = gConfig.servers.filter(function (conf) { return conf.enabled; });
	serverConf.forEach(function (conf) {
		gServers[conf.name] = MCServer(conf);
	});
}) ();

var app = express();

app.use(function(req, res, next) {
	res.header("Access-Control-Allow-Origin", "http://ensky.tw");
	res.header("Access-Control-Allow-Headers", "X-Requested-With");
	next();
});

app.get('/api/start/:name', function (req, res) {
	var server = gServers[req.params.name];
	if (server === undefined) {
		res.status(400).json({ error: 'unknown server name.' });
		return ;
	}

    server.start();
	server.sendStat(res);
});

app.get('/api/stop/:name', function (req, res) {
	var server = gServers[req.params.name];
	if (server === undefined) {
		res.status(400).json({ error: 'unknown server name.' });
		return ;
	}

    server.stop();
	server.sendStat(res);
});

app.get('/api/list', function (req, res) {
	var serverInfo = [];
	Object.keys(gServers).forEach(function (serverName) {
		var server = gServers[serverName];
		serverInfo.push({
			name: serverName,
			stat: server.getStat(),
			conf: server.getConf()
		});
	});

	res.json({servers: serverInfo});
});

app.get('/api/status/:name', function (req, res) {
	var server = gServers[req.params.name];
	if (server === undefined) {
		res.status(400).json({ error: 'unknown server name.' });
		return ;
	}

	server.sendStat(res);
});

var server = app.listen(gConfig.port, function () {
	var host = server.address().address;
	var port = server.address().port;

	log('Example app listening at http://%s:%s', host, port);
});
