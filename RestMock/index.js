var jswiremocklib, jswiremock, stubFor, get, post, put, urlEqualTo, a_response;
jswiremocklib = require('jswiremock'),
    jswiremock = jswiremocklib.jswiremock,
    stubFor = jswiremocklib.stubFor,
    get = jswiremocklib.get,
    post = jswiremocklib.post,
    // put = jswiremocklib.put, not impl
    urlEqualTo = jswiremocklib.urlEqualTo, a_response = jswiremocklib.a_response;

var jswiremock = new jswiremock(5001); //port 

stubFor(jswiremock, get(urlEqualTo("/news/:varying_var/"))
    .willReturn(a_response()
        .withStatus(200)
        .withHeader({"Content-Type": "application/json"})
        .withBody("[{\"__documentType__\":\"news\",\"articleType\":\"news_text\",\"isBreakingNews\":false," +
            "\"title\":\"Einigung zu britischen Sonderwünschen\",\"headline\":\"EU-Gipfel in Brüssel\"," +
            "\"teaserText\":\"Die EU-Staats- und Regierungschefs haben sich bei ihrem Gipfel auf ein Reformpaket " +
            "für Großbritannien verständigt, mit dem ein Austritt des Landes aus der Union verhindert werden soll. " +
            "Das teilte der EU-Gipfelchef Donald Tusk via Twitter mit.\",\"text\":\"<p>Mit der Einigung soll der " +
            "Verbleib Großbritanniens in der EU gesichert werden: </p><blockquote><span>Deal. Unanimous support for " +
            "new settlement for </span><span>#</span>UKinEU, EU-Ratspräsident Donald Tusk via Twitter" +
            "</blockquote><p><span>\"Vereinbarung Großbritannien in der EU steht. Drama vorbei\": " +
            "Mit diesen trockenen Worten teilte als erste die litauische Staatspräsidentin die Einigung mit, " +
            "über die die Staats- und Regierungschefs am Abend beraten hatten.</span><br></p><blockquote>" +
            "<span>\"Agreement #UKinEU done. Drama over.\" Die litauische Staatspräsidentin Dalia Grybauskaite via " +
            "Twitter</span></blockquote><p>Der tschechische Ministerpräsident Bohuslav Sobotka bestätigte kurz darauf " +
            "die Einigung:</p><blockquote>" +
            "</blockquote><h3>Griechenland als Bremser</h3><p>Vorausgegangen waren zähe Verhandlungen und zuletzt ein " +
            "Kompromissvorschlag von EU-Gipfelchef Donald Tusk und EU-Kommissionschef Jean-Claude Juncker. Die griechische " +
            "Delegation wollte ihr Ja zu den Reformwünschen Großbritanniens zuerst an Vorbedingungen knüpfe. Athen wollte" +
            " eigentlich nur zustimmen, wenn gleichzeitig beschlossen würde, dass es bis zum nächsten EU-Treffen Anfang März " +
            "zu keinen Grenzschließungen wegen der Flüchtlingskrise kommt.   die Einigung beinhaltet, ist noch nicht " +
            "durchgesickert. Fest steht aber: Der Weg dorthin war schwierig" +
            "\"images\":[{\"url\":\"https://newsapp-fs-ext.br.de/64t30c9p/64uk0ctq68u38e1g64t3je9j6mrk4.jpeg\"," +
            "\"author\":\"pa / dpa\",\"description\":\"David Cameron und Angela Merkel\",\"meta\":{}}],\"video\":[]," +
            "\"audio\":[],\"origin\":\"\",\"links\":[],\"location\":{\"lat\":50.8503396,\"lon\":4.351710300000036}," +
            "\"tags\":[\"Das Wichtigste\",\"Deutschland & Welt\",\"EU\",\"EU-Gipfel\",\"Großbritannien\",\"David Cameron\"" +
            ",\"Angela Merkel\"],\"status\":\"published\",\"createdAt\":\"2016-02-19T21:46:25.534Z\"" +
            ",\"authorId\":\"user::ad::02739e03-58b5-4efa-b3ee-1ff4e53ee0e8\"," +
            "\"meta\":{\"location\":{\"district\":null,\"zipCode\":\"1000\",\"street\":\"Bisschopsstraat\",\"streetNumber\":\"14\"," +
            "\"country\":\"Belgien\",\"state\":\"Brussel\",\"city\":\"Brussel\",\"suburb\":null,\"poi\":\"Brüssel\"}," +
            "\"priority\":\"0\"},\"publicationDate\":\"2016-02-19T21:46:25.587Z\",\"history\":[{\"event\":\"reviewed\"," +
            "\"timestamp\":\"2016-02-19T21:46:25.527Z\"},{\"event\":\"published\",\"timestamp\":\"2016-02-19T21:46:25.587Z\"}]," +
            "\"id\":\"64uk0ctq68u38e1n70v38ctj6mrk4\",\"updatedAt\":\"2016-02-19T21:46:25.534Z\"," +
            "\"shareLink\":\"https://br24.de/nachrichten/Das%20Wichtigste/einigung-zu-britischen-sonderwuenschen\"}]")));

stubFor(jswiremock, post(urlEqualTo("/rate"), {type: "down", newsid: ":varying_var"})
    .willReturn(a_response()
        .withStatus(200)
        .withStatus(200)
        .withHeader({"Content-Type": "application/json"})
        .withBody("[{\"status\":\"success\"}]")));

stubFor(jswiremock, post(urlEqualTo("/rate/:varying_var/down/"))
    .willReturn(a_response()
        .withStatus(200)
        .withHeader({"Content-Type": "application/json"})
        .withBody("[{\"status\":\"success\"}]")));


stubFor(jswiremock, post(urlEqualTo("/rate/:varying_var/up/"))
    .willReturn(a_response()
        .withStatus(200)
        .withHeader({"Content-Type": "application/json"})
        .withBody("[{\"status\":\"success\"}]")));

stubFor(jswiremock, post(urlEqualTo("/login"), {username: "flo", password: "geheim"})
    .willReturn(a_response()
        .withStatus(200)
        .withHeader({})
        .withBody("")));

// jswiremock.stopJswiremock();
