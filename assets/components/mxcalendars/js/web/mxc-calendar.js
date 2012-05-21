/*
 * mxCalendar AJAX navigation
 * by http://charlesmx.com
 * for ModX Revo
 */
$(function() { 
    var mxcCalPreContent = '';
    var mxcCalNexContent = '';
    var todayContent = '';
    var mxcCalPrev;
    var mxcCalNext;
    var urlParams = {};
    var mxcHistory = [];
    (function () {
        var e,
            a = /\+/g,  // Regex for replacing addition symbol with a space
            r = /([^&=]+)=?([^&]*)/g,
            d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
            q = window.location.search.substring(1);

        while (e = r.exec(q))
           urlParams[d(e[1])] = d(e[2]);
    })();
    
    function ajaxmxc(){
        mxcCalNext = document.getElementById("mxcnextlnk");
        mxcCalPrev = document.getElementById("mxcprevlnk");
        if(mxcCalNext) {
            ajaxObj = '';
            nidx = mxcCalNext.href.indexOf("dt=");
            if(nidx != -1){
                ajaxObj = mxcCalNext.href.substring((nidx + 3),(nidx + 10));
                if(!mxcHistory[ajaxObj]){
                    $.get(mxcCalNext.href+"&imajax=1", {},
                       function(data){
                         mxcCalNexContent = data;
                         mxcHistory[ajaxObj] = data;
                       });
                } else {
                    mxcCalNexContent = mxcHistory[ajaxObj];
                }     
            }
        }
        if(mxcCalPrev) {
            ajaxObjP = '';
            nidxp = mxcCalPrev.href.indexOf("dt=");
            if(nidxp != -1){
                ajaxObjP = mxcCalPrev.href.substring((nidxp + 3),(nidxp + 10));
                if(!mxcHistory[ajaxObjP]){
                    $.get(mxcCalPrev.href+"&imajax=1", {},
                       function(data){
                         mxcCalPreContent = data;
                         mxcHistory[ajaxObjP] = data;
                       });
                } else {
                    mxcCalPreContent = mxcHistory[ajaxObjP];
                }     
            }
        }
        if(modalActive){
            Shadowbox.teardown('.mxcmodal');
            Shadowbox.clearCache();
            Shadowbox.setup(".mxcmodal", {
                            modal: true
            });
        }
    }
    function addHistory(url){
        var stateObj = {};
        if(url)
        history.pushState(stateObj, "Calendar", url);
    }
    $("#mxcnextlnk").live("click",function(event){
        event.preventDefault();
        $("#calbody").html(mxcCalNexContent);
        //addHistory(mxcCalNext);
        ajaxmxc();
    });
    $("#mxcprevlnk").live("click",function(event){
        event.preventDefault();
        $("#calbody").html(mxcCalPreContent);
        //addHistory(mxcCalPrev);
        ajaxmxc();
    });
    $("#mxctodaylnk").live("click",function(event){
        event.preventDefault();
        if(todayContent != ''){
            $("#calbody").html(todayContent);
            ajaxmxc();
        } else {
            $.get(this.href+"&imajax=1", {},
               function(data){
                 todayContent = data;
                 $("#calbody").html(todayContent);
                 ajaxmxc();
               });
        }
    })
    //-- Get today's content
    if(document.getElementById("mxctodaylnk") != null && todayContent == '')
    $.get(document.getElementById("mxctodaylnk").href+"&imajax=1", {},
       function(data){
         todayContent = data;
       });
    ajaxmxc();
    
});

