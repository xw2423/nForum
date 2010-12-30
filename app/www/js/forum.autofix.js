function iframeAutoFit() {
    try {
        if(window!=parent) {
            var a = parent.document.getElementsByTagName("iframe");
            for(var i=0; i<a.length; i++) {
                if(a[i].contentWindow == window) {
                    var h1=0, h2=0, d=document, dd=d.documentElement,oph=a[i].parentNode.scrollHeight,oh = a[i].scrollHeight;
                    //a[i].parentNode.style.height = a[i].offsetHeight +"px";
                    a[i].style.height = "10px";
                    if(dd && dd.scrollHeight) h1=dd.scrollHeight;
                    if(d.body) h2=d.body.scrollHeight;
                    var h=Math.max(h1, h2);
                    if(document.all) {h += 4;}
                    if(window.opera) {h += 1;}
                    a[i].style.height = h +"px";
                    //a[i].parentNode.style.height = (oph+h-oh) + "px";
                }
            }
        }
    } catch (ex){}
}
$(iframeAutoFit);
