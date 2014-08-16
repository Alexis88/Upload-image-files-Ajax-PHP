var form = document.getElementsByTagName("form")[0],
		   output = document.getElementById("output"),
           ajax = function(form, ouput){
		   		var xhr = window.XMLHttpRequest ? 
	        			  new XMLHttpRequest() : 
 	 	 	 			  new ActiveXObject("Microsoft.XMLHTTP") || 
 	 	 	 			  new ActiveXObject("Msxml2.XMLHTTP"),
 	 	 	 			  elements = form.elements,
 	 	 	 			  total = elements.length,
 	 	 	 			  url = form.action,
 	 	 	 			  data = new FormData();
    
				for (var i = 0; i < total; i++){
                	if (elements[i].type == "file"){
	               		var files = elements[i].files,
							totalFiles = files.length;

					for (var j = 0; j < totalFiles; j++){
	           			data.append(elements[i].name + "_" + j, files[j]);
	            	}
	            }
	            else{
	            	data.append(elements[i].name, elements[i].value);
	            }
			}
			
			xhr.open("POST", url, true);
			xhr.onreadystatechange = function(){
	        	if (xhr.readyState == 4){
	            	output.innerHTML = null;
	            	switch (xhr.status){
		            	case 200:
		                	var response = JSON.parse(xhr.responseText);
		                    if (response.ok == "yes"){
			                	var filesResponse = response.dataFiles,
			                    	totalFilesResponse = filesResponse.length;

								for (var k = 0; k < totalFilesResponse; k++){
			                    	var img = document.createElement("img");
			                        img.src = filesResponse[k];
			                        output.appendChild(img);
								}
							}
			                else{
			                	output.innerHTML = "Error";
							}
		                    break;
						case 404:
		                	output.innerHTML = "404 Not Found";
		                    break;
						default:
		                	output.innerHTML = "Error: " + xhr.status; 
		                    break;
					}
				}
			}
	        xhr.send(data);
		};

form.addEventListener("submit", function(event){
	event.preventDefault();
	ajax(this, output);
}, false);
