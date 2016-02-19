var jswiremocklib, jswiremock, stubFor, get, urlEqualTo, a_response;
jswiremocklib = require('jswiremock'), jswiremock = jswiremocklib.jswiremock, stubFor = jswiremocklib.stubFor, get = jswiremocklib.get, urlEqualTo = jswiremocklib.urlEqualTo, a_response = jswiremocklib.a_response;
 
var jswiremock = new jswiremock(5001); //port 
 
stubFor(jswiremock, get(urlEqualTo("/account/:varying_var/delete/"))
    .willReturn(a_response()
        .withStatus(200)
        .withHeader({"Content-Type": "application/json"})
        .withBody("[{\"status\":\"success\"}]")));
        
stubFor(jswiremock, post(urlEqualTo("/login"), {username: "captainkirk", password: "enterprise"})
    .willReturn(a_response()
        .withStatus(200)
        .withHeader({})
        .withBody("")));
 
jswiremock.stopJswiremock();
