<!DOCTYPE html>
<html>
	<head>
		<title>Humble Home Page</title>
		<link rel="stylesheet" type="text/css" href="/css/bootstrap" />
		<style type="text/css">
			.column {
				display: inline-block; box-style: border-box; vertical-align: top
			}
			.side-column {
				width: 15%; background-color: #445348;
		</style>
		<script type="text/javascript" src="/js/jquery"></script>
		<script type="text/javascript">
		$(document).ready(function () {
			$(document).resize(function () {
				$('.column').height($(document).height());
			}).resize();
		});
		</script>
	</head>
	<body>
		<div id='left-column' class='column side-column'>
		</div><div id='center-column' class='column' style='width: 70%; background-image: url(/images/paradigm/bg_graph.png)'>
		<div style='font-size: 4.5em;  margin-left: auto; margin-right: auto; margin-top: 50px;  text-align: center'>Humble.</div>
		<div style='font-size: 1em; letter-spacing: 0.5px; text-align: center; margin-top: 10px'>Dedicated to <a href="https://en.wikipedia.org/wiki/Edsger_W._Dijkstra" target="_BLANK" style="color: blue">Edsger Dijkstra</a>.</div>
		<div style='font-size: 1em; letter-spacing: 2px; text-align: center; margin-top: 20px'>by Rick Myers.</div>
		<div style='font-size: 1em; text-align: center; margin-top: 50px; width: 500px; margin-right: auto; margin-left: auto'>

		Humble is released to the public under a <a href='https://www.gnu.org/licenses/gpl-3.0-standalone.html' target='_BLANK'>GNU GPL v3 License</a>.
		</div>
		<div style="width: 30%; margin-left: auto; margin-right: auto; margin-top: 65px">
		Some useful locations...<br /><br />
		<ul>
			<li> &&HOME&& - location for the main page of your SPA (Single Page Application)</li>
			<li> <a style="color: blue" href="https://humble.enicity.com" target="_BLANK">https://humble.enicity.com</a> - website with extensive documentation and videos for the Humble framework</li>
			<li> <a style="color: blue" href="/admin" target="_BLANK">/admin</a> - administration page for your application</li>
		</ul>
		<br /><br />
		Best of luck in everything you are trying to accomplish!

</div>
		</div><div id='right-column' class='column side-column'>
		</div>
	</body>
</html>