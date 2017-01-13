/**
 *
 * Objeto literal para o uso da metodologia Ajax.
 * Documentacao disponivel em: http://code.google.com/p/jscomponentes/wiki/Ajax
 * @author: Edy Segura - edy@segura.pro.br
 *
 */
var Ajax = {

	loading: null,
	
	getXHR: function() {
		var httpRequest;
		
		//instanciando o objeto XMLHttpRequest
		try {
			httpRequest = new XMLHttpRequest();
		}
		catch(e1) {
			try {
				httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch(e2) {
				try {
					httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch(e3) {
					httpRequest = false;
				}
			}
		}
		
		return httpRequest;
	},
	
	
	createLoading: function() {
		var loading = document.createElement('p');
		
		loading.id = "ajax-loading";
		loading.className = "loading";
		loading.innerHTML = "Carregando...";
		document.body.appendChild(loading);
		
		Ajax.loading = loading;
	},
	
	
	removeLoading: function() {
		if(Ajax.loading) {
			Ajax.loading.parentNode.removeChild(Ajax.loading);
			Ajax.loading = null;
		}
	},
	

	addRequest: function(params) {
	},
	
	
	//old alias (deprecated)
	run: function(params) {
		Ajax.request(params);
	},
	

	request: function(params) {
		var httpRequest = Ajax.getXHR();
		var result = true;
		
		if(httpRequest) {
			var method = (params.method) ? params.method : "GET";
			var async  = (typeof params.async == 'boolean') ? params.async : true;
			if(params.loading) Ajax.createLoading();
			
			httpRequest.open(method, params.url, async);
			httpRequest.setRequestHeader("Cache-Control", "no-cache, must-revalidate");
			httpRequest.setRequestHeader("Pragma", "no-cache");
			
			if(method.toUpperCase() == "POST") {
				httpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			
			if(params.response == "xml" && httpRequest.overrideMimeType) {
				httpRequest.overrideMimeType('text/xml');
			}
			
			httpRequest.onreadystatechange = function() {
				if(httpRequest.readyState == 4) {
					if(httpRequest.status == 200 || params.update) {
						
						if(params.callback) {
							params.callback(
								(params.response == "xml") ? httpRequest.responseXML : httpRequest.responseText,
								(params.params) ? params.params : null
							);
						}
						
						if(params.loading) Ajax.removeLoading();
					}
					else {
						
						if(params.callerro) {
							params.callerro(
								httpRequest.status,
								httpRequest.statusText,
								(params.params) ? params.params : null
							);
						}
						else {
							var message = new String();
							
							message += "HTTP Status: " + httpRequest.status + "\n";
							message += "Message: ";
							message += (httpRequest.statusText) ? httpRequest.statusText : "Unknown";
							
							alert(message);
						}
						
						if(params.loading) Ajax.removeLoading();
					}
				}
			};
			
			httpRequest.send((params.send) ? params.send : null);
			delete httpRequest;
		}
		else {
			throw new Error("Your browser does not support XMLHttpRequest");
			result = false;
		}
		
		return result;
	}

};