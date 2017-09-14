<script>
	document.onkeydown = function (e) {
		e = e || window.event; // Get event
		if (e.ctrlKey) {
			var c = e.which || e.keyCode; // Get key code
			switch (c) {
				case 74: // Block Ctrl + W -- Not work in Chrome
				console.log('pressionou CTRL + J');
				e.preventDefault();
				e.stopPropagation();
				break;
			}
		}
	};

	function empty(vlr,zero){
		zero = zero == null ? true : false ;
		if((vlr == undefined || vlr == null || vlr == '' || vlr == 0) && (zero) )
			return true;
		else if(isNaN(Number(vlr))){
			if(vlr == undefined || vlr == null || vlr == '')
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-30460729-2', 'auto');
	
	var user_logged = {
		id_empreendimento: '<?php echo $_SESSION['user']['id_empreendimento']; ?>',
		nme_empreendimento: '<?php echo $_SESSION['user']['nome_empreendimento']; ?>',
		id_usuario: '<?php echo $_SESSION['user']['id']; ?>',
		nme_usuario: '<?php echo $_SESSION['user']['nme_usuario']; ?>'
	};

	if(!empty(user_logged.id_usuario))
		ga('set', 'dimension1', user_logged.id_usuario.toString());
	
	if(!empty(user_logged.nme_usuario))
		ga('set', 'dimension2', user_logged.nme_usuario);
	
	if(!empty(user_logged.id_empreendimento))
		ga('set', 'dimension3', user_logged.id_empreendimento.toString());
	
	if(!empty(user_logged.nme_empreendimento))
		ga('set', 'dimension4', user_logged.nme_empreendimento);
	
	ga('send', 'pageview');
</script>
<script type="text/javascript">
    window.smartlook||(function(d) {
    var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
    var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
    c.charset='utf-8';c.src='https://rec.smartlook.com/recorder.js';h.appendChild(c);
    })(document);
    smartlook('init', '357adfa3bc6959755c266912a95bfdd6e40cdfd0');
</script>